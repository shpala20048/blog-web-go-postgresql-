package service

import (
	"context"
	"fmt"
	"log"
	"time"

	"blog-api/internal/model"
	"blog-api/internal/repository"

	firebase "firebase.google.com/go/v4"
	"firebase.google.com/go/v4/auth"
	"google.golang.org/api/option"
)

type FirebaseService struct {
	Client *auth.Client
}

func NewFirebaseService(credentialPath string) (*FirebaseService, error) {
	opt := option.WithCredentialsFile(credentialPath)
	app, err := firebase.NewApp(context.Background(), nil, opt)
	if err != nil {
		return nil, fmt.Errorf("error initializing Firebase app: %v", err)
	}

	client, err := app.Auth(context.Background())
	if err != nil {
		return nil, fmt.Errorf("error getting Firebase auth client: %v", err)
	}

	log.Println("Firebase Auth initialized successfully")
	return &FirebaseService{Client: client}, nil
}

func (s *FirebaseService) VerifyIDToken(idToken string) (*model.FirebaseUser, error) {
	token, err := s.Client.VerifyIDToken(context.Background(), idToken)
	if err != nil {
		return nil, fmt.Errorf("invalid ID token: %v", err)
	}

	firebaseUser := &model.FirebaseUser{
		UID:           token.UID,
		Email:         token.Claims["email"].(string),
		EmailVerified: token.Claims["email_verified"].(bool),
	}

	if name, ok := token.Claims["name"].(string); ok {
		firebaseUser.DisplayName = name
	}

	if photoURL, ok := token.Claims["picture"].(string); ok {
		firebaseUser.PhotoURL = photoURL
	}

	return firebaseUser, nil
}

func (s *FirebaseService) SendPasswordResetEmail(email string) error {
	link, err := s.Client.PasswordResetLink(context.Background(), email)
	if err != nil {
		return fmt.Errorf("error generating password reset link: %v", err)
	}

	log.Printf("[DEBUG] Password reset link for %s: %s", email, link)
	return nil
}

type AuthService struct {
	AuthRepo  *repository.AuthRepository
	Firebase  *FirebaseService
	TokenExpiry time.Duration
}

func NewAuthService(authRepo *repository.AuthRepository, firebaseSvc *FirebaseService) *AuthService {
	return &AuthService{
		AuthRepo:    authRepo,
		Firebase:    firebaseSvc,
		TokenExpiry: 7 * 24 * time.Hour,
	}
}

func (s *AuthService) Login(idToken, ipAddress, userAgent string) (*model.AuthResponse, error) {
	rl, err := s.AuthRepo.GetOrCreateRateLimit(ipAddress, "login")
	if err == nil && rl.LockedUntil.Valid && rl.LockedUntil.Time.After(time.Now()) {
		return &model.AuthResponse{
			Success: false,
			Message: "Слишком много попыток. Попробуйте позже",
		}, nil
	}

	firebaseUser, err := s.Firebase.VerifyIDToken(idToken)
	if err != nil {
		s.AuthRepo.IncrementRateLimit(ipAddress, "login")
		s.AuthRepo.LogAuth(nil, "login", ipAddress, userAgent, false, err.Error())

		attempts := rl.Attempts + 1
		if attempts >= 5 {
			s.AuthRepo.LockRateLimit(ipAddress, "login", time.Now().Add(15*time.Minute))
			return &model.AuthResponse{
				Success: false,
				Message: "Слишком много попыток. Попробуйте через 15 минут",
			}, nil
		}

		return &model.AuthResponse{
			Success: false,
			Message: "Неверный токен",
		}, nil
	}

	if !firebaseUser.EmailVerified {
		s.AuthRepo.LogAuth(nil, "login", ipAddress, userAgent, false, "Email not verified")
		return &model.AuthResponse{
			Success: false,
			Message: "Подтвердите email перед входом",
		}, nil
	}

	userID, err := s.AuthRepo.CreateUser(
		firebaseUser.UID,
		firebaseUser.Email,
		firebaseUser.DisplayName,
		firebaseUser.EmailVerified,
	)
	if err != nil {
		return nil, fmt.Errorf("error creating user: %v", err)
	}

	s.AuthRepo.UpdateLastLogin(userID)

	token, err := s.AuthRepo.CreateSession(userID, ipAddress, userAgent, s.TokenExpiry)
	if err != nil {
		return nil, fmt.Errorf("error creating session: %v", err)
	}

	s.AuthRepo.ResetRateLimit(ipAddress, "login")
	s.AuthRepo.LogAuth(&userID, "login", ipAddress, userAgent, true, "")

	user, _ := s.AuthRepo.GetUserByID(userID)

	return &model.AuthResponse{
		Success: true,
		Message: "Успешный вход",
		Token:   token,
		User:    user,
	}, nil
}

func (s *AuthService) Verify(token, ipAddress string) (*model.AuthResponse, error) {
	session, err := s.AuthRepo.GetSession(token)
	if err != nil {
		return &model.AuthResponse{
			Success: false,
			Message: "Недействительная сессия",
		}, nil
	}

	user, err := s.AuthRepo.GetUserByID(session.UserID)
	if err != nil {
		return &model.AuthResponse{
			Success: false,
			Message: "Пользователь не найден",
		}, nil
	}

	return &model.AuthResponse{
		Success: true,
		Message: "Сессия действительна",
		User:    user,
	}, nil
}

func (s *AuthService) Logout(token, ipAddress string) error {
	session, err := s.AuthRepo.GetSession(token)
	if err == nil && session != nil {
		userID := session.UserID
		s.AuthRepo.LogAuth(&userID, "logout", ipAddress, "", true, "")
	}

	return s.AuthRepo.DeleteSession(token)
}

func (s *AuthService) ResetPassword(email string) error {
	if err := s.Firebase.SendPasswordResetEmail(email); err != nil {
		return err
	}
	return nil
}

func (s *AuthService) CleanExpiredSessions() {
	go func() {
		for {
			s.AuthRepo.DeleteExpiredSessions()
			time.Sleep(1 * time.Hour)
		}
	}()
}
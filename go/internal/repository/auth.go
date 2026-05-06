package repository

import (
	"database/sql"
	"time"

	"blog-api/internal/model"

	"github.com/google/uuid"
)

type AuthRepository struct {
	DB *sql.DB
}

func NewAuthRepository(db *sql.DB) *AuthRepository {
	return &AuthRepository{DB: db}
}

func (r *AuthRepository) CreateUser(firebaseUID, email, displayName string, emailVerified bool) (int, error) {
	var id int
	err := r.DB.QueryRow(`
		INSERT INTO users (firebase_uid, email, display_name, email_verified)
		VALUES ($1, $2, $3, $4)
		ON CONFLICT (firebase_uid) DO UPDATE 
		SET email = EXCLUDED.email, display_name = EXCLUDED.display_name, 
		    email_verified = EXCLUDED.email_verified, updated_at = NOW()
		RETURNING id
	`, firebaseUID, email, displayName, emailVerified).Scan(&id)
	return id, err
}

func (r *AuthRepository) GetUserByFirebaseUID(firebaseUID string) (*model.User, error) {
	var u model.User
	err := r.DB.QueryRow(`
		SELECT id, firebase_uid, email, display_name, email_verified, last_login, created_at, updated_at
		FROM users WHERE firebase_uid = $1
	`, firebaseUID).Scan(&u.ID, &u.FirebaseUID, &u.Email, &u.DisplayName, &u.EmailVerified, &u.LastLogin, &u.CreatedAt, &u.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return &u, nil
}

func (r *AuthRepository) GetUserByID(id int) (*model.User, error) {
	var u model.User
	err := r.DB.QueryRow(`
		SELECT id, firebase_uid, email, display_name, email_verified, last_login, created_at, updated_at
		FROM users WHERE id = $1
	`, id).Scan(&u.ID, &u.FirebaseUID, &u.Email, &u.DisplayName, &u.EmailVerified, &u.LastLogin, &u.CreatedAt, &u.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return &u, nil
}

func (r *AuthRepository) UpdateLastLogin(userID int) error {
	_, err := r.DB.Exec(`UPDATE users SET last_login = NOW(), updated_at = NOW() WHERE id = $1`, userID)
	return err
}

func (r *AuthRepository) CreateSession(userID int, ipAddress, userAgent string, expiresIn time.Duration) (string, error) {
	token := uuid.New().String()
	expiresAt := time.Now().Add(expiresIn)

	_, err := r.DB.Exec(`
		INSERT INTO sessions (user_id, token, ip_address, user_agent, expires_at)
		VALUES ($1, $2, $3, $4, $5)
	`, userID, token, ipAddress, userAgent, expiresAt)
	return token, err
}

func (r *AuthRepository) GetSession(token string) (*model.Session, error) {
	var s model.Session
	err := r.DB.QueryRow(`
		SELECT id, user_id, token, ip_address, user_agent, expires_at, created_at
		FROM sessions 
		WHERE token = $1 AND expires_at > NOW()
	`, token).Scan(&s.ID, &s.UserID, &s.Token, &s.IPAddress, &s.UserAgent, &s.ExpiresAt, &s.CreatedAt)
	if err != nil {
		return nil, err
	}
	return &s, nil
}

func (r *AuthRepository) DeleteSession(token string) error {
	_, err := r.DB.Exec(`DELETE FROM sessions WHERE token = $1`, token)
	return err
}

func (r *AuthRepository) DeleteExpiredSessions() error {
	_, err := r.DB.Exec(`DELETE FROM sessions WHERE expires_at < NOW()`)
	return err
}

func (r *AuthRepository) DeleteUserSessions(userID int) error {
	_, err := r.DB.Exec(`DELETE FROM sessions WHERE user_id = $1`, userID)
	return err
}

func (r *AuthRepository) LogAuth(userID *int, action, ipAddress, userAgent string, success bool, details string) error {
	_, err := r.DB.Exec(`
		INSERT INTO auth_logs (user_id, action, ip_address, user_agent, success, details)
		VALUES ($1, $2, $3, $4, $5, $6)
	`, userID, action, ipAddress, userAgent, success, details)
	return err
}

func (r *AuthRepository) GetOrCreateRateLimit(identifier, action string) (*model.RateLimit, error) {
	var rl model.RateLimit
	err := r.DB.QueryRow(`
		SELECT id, identifier, action, attempts, locked_until, created_at, updated_at
		FROM rate_limits 
		WHERE identifier = $1 AND action = $2
	`, identifier, action).Scan(&rl.ID, &rl.Identifier, &rl.Action, &rl.Attempts, &rl.LockedUntil, &rl.CreatedAt, &rl.UpdatedAt)

	if err == sql.ErrNoRows {
		err = r.DB.QueryRow(`
			INSERT INTO rate_limits (identifier, action) VALUES ($1, $2)
			ON CONFLICT (identifier, action) DO NOTHING
			RETURNING id, identifier, action, attempts, locked_until, created_at, updated_at
		`, identifier, action).Scan(&rl.ID, &rl.Identifier, &rl.Action, &rl.Attempts, &rl.LockedUntil, &rl.CreatedAt, &rl.UpdatedAt)
		if err != nil {
			return nil, err
		}
	}
	return &rl, nil
}

func (r *AuthRepository) IncrementRateLimit(identifier, action string) error {
	_, err := r.DB.Exec(`
		INSERT INTO rate_limits (identifier, action, attempts) VALUES ($1, $2, 1)
		ON CONFLICT (identifier, action) DO UPDATE 
		SET attempts = rate_limits.attempts + 1, updated_at = NOW()
	`, identifier, action)
	return err
}

func (r *AuthRepository) ResetRateLimit(identifier, action string) error {
	_, err := r.DB.Exec(`
		UPDATE rate_limits SET attempts = 0, locked_until = NULL, updated_at = NOW()
		WHERE identifier = $1 AND action = $2
	`, identifier, action)
	return err
}

func (r *AuthRepository) LockRateLimit(identifier, action string, lockedUntil time.Time) error {
	_, err := r.DB.Exec(`
		UPDATE rate_limits SET locked_until = $3, updated_at = NOW()
		WHERE identifier = $1 AND action = $2
	`, identifier, action, lockedUntil)
	return err
}
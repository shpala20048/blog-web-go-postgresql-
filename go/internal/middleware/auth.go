package middleware

import (
	"net/http"
	"strings"

	"blog-api/internal/model"
	"blog-api/internal/repository"

	"github.com/gin-gonic/gin"
)

type AuthMiddleware struct {
	AuthRepo *repository.AuthRepository
}

func NewAuthMiddleware(authRepo *repository.AuthRepository) *AuthMiddleware {
	return &AuthMiddleware{AuthRepo: authRepo}
}

func (m *AuthMiddleware) RequireAuth() gin.HandlerFunc {
	return func(c *gin.Context) {
		token := c.GetHeader("Authorization")
		if token == "" {
			c.JSON(http.StatusUnauthorized, model.AuthResponse{
				Success: false,
				Message: "Требуется авторизация",
			})
			c.Abort()
			return
		}

		token = strings.TrimPrefix(token, "Bearer ")
		if token == "" {
			c.JSON(http.StatusUnauthorized, model.AuthResponse{
				Success: false,
				Message: "Недействительный токен",
			})
			c.Abort()
			return
		}

		session, err := m.AuthRepo.GetSession(token)
		if err != nil {
			c.JSON(http.StatusUnauthorized, model.AuthResponse{
				Success: false,
				Message: "Сессия истекла или недействительна",
			})
			c.Abort()
			return
		}

		user, err := m.AuthRepo.GetUserByID(session.UserID)
		if err != nil {
			c.JSON(http.StatusUnauthorized, model.AuthResponse{
				Success: false,
				Message: "Пользователь не найден",
			})
			c.Abort()
			return
		}

		c.Set("user", user)
		c.Set("session", session)
		c.Next()
	}
}

func GetUser(c *gin.Context) *model.User {
	user, exists := c.Get("user")
	if !exists {
		return nil
	}
	return user.(*model.User)
}

func GetSession(c *gin.Context) *model.Session {
	session, exists := c.Get("session")
	if !exists {
		return nil
	}
	return session.(*model.Session)
}
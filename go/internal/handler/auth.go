package handler

import (
	"net/http"

	"blog-api/internal/model"
	"blog-api/internal/service"

	"github.com/gin-gonic/gin"
)

type AuthHandler struct {
	AuthService *service.AuthService
}

func NewAuthHandler(authService *service.AuthService) *AuthHandler {
	return &AuthHandler{AuthService: authService}
}

func (h *AuthHandler) Login(c *gin.Context) {
	var req model.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, model.AuthResponse{
			Success: false,
			Message: "id_token обязателен",
		})
		return
	}

	ip := c.ClientIP()
	userAgent := c.Request.UserAgent()

	resp, err := h.AuthService.Login(req.IdToken, ip, userAgent)
	if err != nil {
		c.JSON(http.StatusInternalServerError, model.AuthResponse{
			Success: false,
			Message: "Внутренняя ошибка сервера",
		})
		return
	}

	if !resp.Success {
		c.JSON(http.StatusUnauthorized, resp)
		return
	}

	c.JSON(http.StatusOK, resp)
}

func (h *AuthHandler) Verify(c *gin.Context) {
	token := c.GetHeader("Authorization")
	if token == "" {
		token = c.Query("token")
	}
	token = token[7:]

	ip := c.ClientIP()
	resp, err := h.AuthService.Verify(token, ip)
	if err != nil {
		c.JSON(http.StatusInternalServerError, model.AuthResponse{
			Success: false,
			Message: "Внутренняя ошибка сервера",
		})
		return
	}

	if !resp.Success {
		c.JSON(http.StatusUnauthorized, resp)
		return
	}

	c.JSON(http.StatusOK, resp)
}

func (h *AuthHandler) Logout(c *gin.Context) {
	token := c.GetHeader("Authorization")
	if token == "" {
		token = c.Query("token")
	}
	if len(token) > 7 {
		token = token[7:]
	}

	ip := c.ClientIP()
	err := h.AuthService.Logout(token, ip)
	if err != nil {
		c.JSON(http.StatusInternalServerError, model.AuthResponse{
			Success: false,
			Message: "Ошибка при выходе",
		})
		return
	}

	c.JSON(http.StatusOK, model.AuthResponse{
		Success: true,
		Message: "Выход выполнен",
	})
}

func (h *AuthHandler) ResetPassword(c *gin.Context) {
	var req struct {
		Email string `json:"email" binding:"required,email"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, model.AuthResponse{
			Success: false,
			Message: "email обязателен",
		})
		return
	}

	err := h.AuthService.ResetPassword(req.Email)
	if err != nil {
		c.JSON(http.StatusInternalServerError, model.AuthResponse{
			Success: false,
			Message: "Не удалось отправить ссылку для сброса пароля",
		})
		return
	}

	c.JSON(http.StatusOK, model.AuthResponse{
		Success: true,
		Message: "Ссылка для сброса пароля отправлена на email",
	})
}

func (h *AuthHandler) Me(c *gin.Context) {
	user := c.MustGet("user").(*model.User)
	c.JSON(http.StatusOK, user)
}
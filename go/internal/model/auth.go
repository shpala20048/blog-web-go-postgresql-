package model

import (
	"database/sql"
	"time"
)

type User struct {
	ID            int          `json:"id"`
	FirebaseUID   string       `json:"firebase_uid"`
	Email         string       `json:"email"`
	DisplayName  sql.NullString `json:"display_name,omitempty"`
	EmailVerified bool         `json:"email_verified"`
	LastLogin     sql.NullTime  `json:"last_login,omitempty"`
	CreatedAt    time.Time    `json:"created_at"`
	UpdatedAt    time.Time    `json:"updated_at"`
}

type Session struct {
	ID         int       `json:"id"`
	UserID     int       `json:"user_id"`
	Token      string    `json:"token"`
	IPAddress  string    `json:"ip_address,omitempty"`
	UserAgent  string    `json:"user_agent,omitempty"`
	ExpiresAt time.Time `json:"expires_at"`
	CreatedAt time.Time `json:"created_at"`
}

type AuthLog struct {
	ID        int       `json:"id"`
	UserID   sql.NullInt64 `json:"user_id,omitempty"`
	Action   string    `json:"action"`
	IPAddress string   `json:"ip_address,omitempty"`
	UserAgent string   `json:"user_agent,omitempty"`
	Success  bool      `json:"success"`
	Details  string    `json:"details,omitempty"`
	CreatedAt time.Time `json:"created_at"`
}

type RateLimit struct {
	ID         int          `json:"id"`
	Identifier string     `json:"identifier"`
	Action     string     `json:"action"`
	Attempts  int        `json:"attempts"`
	LockedUntil sql.NullTime `json:"locked_until,omitempty"`
	CreatedAt  time.Time   `json:"created_at"`
	UpdatedAt time.Time   `json:"updated_at"`
}

type LoginRequest struct {
	IdToken string `json:"id_token" binding:"required"`
}

type VerifyRequest struct {
	Token string `json:"token" binding:"required"`
}

type LogoutRequest struct {
	Token string `json:"token"`
}

type AuthResponse struct {
	Success bool   `json:"success"`
	Message string `json:"message,omitempty"`
	Token   string `json:"token,omitempty"`
	User   *User  `json:"user,omitempty"`
}

type FirebaseUser struct {
	UID           string `json:"uid"`
	Email         string `json:"email"`
	EmailVerified bool   `json:"email_verified"`
	DisplayName   string `json:"display_name,omitempty"`
	PhotoURL     string `json:"photo_url,omitempty"`
}
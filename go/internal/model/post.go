package model

import (
	"strings"
	"time"
)

const (
	MaxTitleLength   = 200
	MaxContentLength = 5000
)

type Post struct {
	ID        int       `json:"id"`
	Title     string    `json:"title"`
	Content   string    `json:"content"`
	Category  string    `json:"category"`
	UserID    int       `json:"user_id,omitempty"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

type CreatePostRequest struct {
	Title    string `json:"title" binding:"required"`
	Content  string `json:"content" binding:"required"`
	Category string `json:"category" binding:"required"`
}

func (r *CreatePostRequest) Validate() error {
	r.Title = strings.TrimSpace(r.Title)
	r.Content = strings.TrimSpace(r.Content)
	r.Category = strings.TrimSpace(r.Category)
	
	if len(r.Title) == 0 || len(r.Title) > MaxTitleLength {
		return &ValidationError{Field: "title", Message: "заголовок должен быть от 1 до 200 символов"}
	}
	if len(r.Content) == 0 || len(r.Content) > MaxContentLength {
		return &ValidationError{Field: "content", Message: "содержание должно быть от 1 до 5000 символов"}
	}
	if len(r.Category) == 0 {
		return &ValidationError{Field: "category", Message: "категория обязательна"}
	}
	return nil
}

type UpdatePostRequest struct {
	Title    string `json:"title"`
	Content  string `json:"content"`
	Category string `json:"category"`
}

func (r *UpdatePostRequest) Validate() error {
	r.Title = strings.TrimSpace(r.Title)
	r.Content = strings.TrimSpace(r.Content)
	r.Category = strings.TrimSpace(r.Category)
	
	if len(r.Title) > MaxTitleLength {
		return &ValidationError{Field: "title", Message: "заголовок не может превышать 200 символов"}
	}
	if len(r.Content) > MaxContentLength {
		return &ValidationError{Field: "content", Message: "содержание не может превышать 5000 символов"}
	}
	return nil
}

type ValidationError struct {
	Field   string
	Message string
}

func (e *ValidationError) Error() string {
	return e.Message
}
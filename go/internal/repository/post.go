package repository

import (
	"database/sql"
	"fmt"
	"blog-api/internal/model"
)

type PostRepository struct {
	DB *sql.DB
}

func NewPostRepository(db *sql.DB) *PostRepository {
	return &PostRepository{DB: db}
}

func (r *PostRepository) GetAll() ([]model.Post, error) {
	rows, err := r.DB.Query(`
		SELECT id, title, content, category, user_id, created_at, updated_at 
		FROM posts 
		ORDER BY created_at DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var posts []model.Post
	for rows.Next() {
		var p model.Post
		if err := rows.Scan(&p.ID, &p.Title, &p.Content, &p.Category, &p.UserID, &p.CreatedAt, &p.UpdatedAt); err != nil {
			return nil, err
		}
		posts = append(posts, p)
	}
	return posts, nil
}

func (r *PostRepository) GetByID(id int) (*model.Post, error) {
	var p model.Post
	err := r.DB.QueryRow(`
		SELECT id, title, content, category, user_id, created_at, updated_at 
		FROM posts WHERE id = $1
	`, id).Scan(&p.ID, &p.Title, &p.Content, &p.Category, &p.UserID, &p.CreatedAt, &p.UpdatedAt)
	if err != nil {
		return nil, err
	}
	return &p, nil
}

func (r *PostRepository) Create(post model.CreatePostRequest, userID int) (int, error) {
	var id int
	err := r.DB.QueryRow(`
		INSERT INTO posts (title, content, category, user_id) 
		VALUES ($1, $2, $3, $4) 
		RETURNING id
	`, post.Title, post.Content, post.Category, userID).Scan(&id)
	return id, err
}

func (r *PostRepository) Update(id int, post model.UpdatePostRequest, userID int) error {
	result, err := r.DB.Exec(`
		UPDATE posts 
		SET title = COALESCE(NULLIF($1, ''), title),
		    content = COALESCE(NULLIF($2, ''), content),
		    category = COALESCE(NULLIF($3, ''), category),
		    updated_at = NOW()
		WHERE id = $4 AND user_id = $5
	`, post.Title, post.Content, post.Category, id, userID)
	if err != nil {
		return err
	}
	rows, _ := result.RowsAffected()
	if rows == 0 {
		return fmt.Errorf("post not found or access denied")
	}
	return nil
}

func (r *PostRepository) Delete(id int, userID int) error {
	result, err := r.DB.Exec("DELETE FROM posts WHERE id = $1 AND user_id = $2", id, userID)
	if err != nil {
		return err
	}
	rows, _ := result.RowsAffected()
	if rows == 0 {
		return fmt.Errorf("post not found or access denied")
	}
	return nil
}

func (r *PostRepository) GetUserID(id int) (int, error) {
	var userID int
	err := r.DB.QueryRow("SELECT user_id FROM posts WHERE id = $1", id).Scan(&userID)
	return userID, err
}
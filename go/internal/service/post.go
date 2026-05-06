package service

import (
	"blog-api/internal/model"
	"blog-api/internal/repository"
)

type PostService struct {
	Repo *repository.PostRepository
}

func NewPostService(repo *repository.PostRepository) *PostService {
	return &PostService{Repo: repo}
}

func (s *PostService) GetAll() ([]model.Post, error) {
	return s.Repo.GetAll()
}

func (s *PostService) GetByID(id int) (*model.Post, error) {
	return s.Repo.GetByID(id)
}

func (s *PostService) Create(post model.CreatePostRequest, userID int) (int, error) {
	return s.Repo.Create(post, userID)
}

func (s *PostService) Update(id int, post model.UpdatePostRequest, userID int) error {
	return s.Repo.Update(id, post, userID)
}

func (s *PostService) Delete(id int, userID int) error {
	return s.Repo.Delete(id, userID)
}

func (s *PostService) GetUserID(id int) (int, error) {
	return s.Repo.GetUserID(id)
}
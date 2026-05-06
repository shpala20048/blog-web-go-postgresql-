package main

import (
	"database/sql"
	"log"
	"net/http"
	"os"

	"blog-api/internal/handler"
	"blog-api/internal/middleware"
	"blog-api/internal/repository"
	"blog-api/internal/service"

	"github.com/gin-gonic/gin"
	_ "github.com/lib/pq"
)

func main() {
	db, err := sql.Open("postgres", "host=localhost port=5432 user=postgres password=79641925598 dbname=blog sslmode=disable")
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if err := db.Ping(); err != nil {
		log.Fatal(err)
	}

	credentialPath := "firebase-config.json"
	if _, err := os.Stat(credentialPath); err != nil {
		log.Println("Warning: firebase-config.json not found, auth will not work")
	}

	firebaseSvc, err := service.NewFirebaseService(credentialPath)
	if err != nil {
		log.Printf("Warning: Firebase initialization failed: %v", err)
	}

	postRepo := repository.NewPostRepository(db)
	postSvc := service.NewPostService(postRepo)
	postHandler := handler.NewPostHandler(postSvc)

	authRepo := repository.NewAuthRepository(db)
	authSvc := service.NewAuthService(authRepo, firebaseSvc)
	authHandler := handler.NewAuthHandler(authSvc)
	authMiddleware := middleware.NewAuthMiddleware(authRepo)

	r := gin.Default()

	r.GET("/api/posts", postHandler.GetAll)
	r.GET("/api/posts/:id", postHandler.GetByID)
	r.POST("/api/posts", postHandler.Create)
	r.PUT("/api/posts/:id", postHandler.Update)
	r.DELETE("/api/posts/:id", postHandler.Delete)

	r.POST("/auth/login", authHandler.Login)
	r.POST("/auth/logout", authHandler.Logout)
	r.GET("/auth/verify", authHandler.Verify)
	r.POST("/auth/reset-password", authHandler.ResetPassword)

	protected := r.Group("/auth")
	protected.Use(authMiddleware.RequireAuth())
	{
		protected.GET("/me", authHandler.Me)
	}

	log.Println("Server running on :8080")
	log.Fatal(http.ListenAndServe(":8080", r))
}
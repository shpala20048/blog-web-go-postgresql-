$env:PGPASSWORD = "79641925598"
psql -h localhost -U postgres -c "CREATE DATABASE blog;"
psql -h localhost -U postgres -d blog -f "go/migrations/001_posts.sql"
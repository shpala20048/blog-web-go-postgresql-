$env:PGPASSWORD = "79641925598"
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -h localhost -U postgres -d blog -c "ALTER TABLE posts ALTER COLUMN title TYPE VARCHAR(200);"
ALTER TABLE posts ADD COLUMN user_id INTEGER REFERENCES users(id);

CREATE INDEX idx_posts_user_id ON posts(user_id);
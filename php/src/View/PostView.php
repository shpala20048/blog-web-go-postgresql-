<?php

class PostView
{
    public function renderList(array $posts): void
    {
        if (empty($posts)) {
            echo '<div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3>Пока нет статей</h3>
                <p>Напишите первую статью, чтобы начать делиться знаниями</p>
                <a href="/post.php" class="add-post-btn">Написать статью</a>
            </div>';
            return;
        }
        
        echo '<div class="posts">';
        foreach ($posts as $post) {
            echo '<article class="post-card">';
            echo '<div class="post-header">';
            echo '<h2 class="post-title"><a href="/post.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h2>';
            echo '<span class="category-badge">' . htmlspecialchars($post['category']) . '</span>';
            echo '</div>';
            echo '<div class="post-meta">';
            echo '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ' . date('d.m.Y в H:i', strtotime($post['created_at'])) . '</span>';
            echo '</div>';
            echo '<p class="post-content">' . htmlspecialchars(mb_substr($post['content'], 0, 400)) . (strlen($post['content']) > 400 ? '...' : '') . '</p>';
            echo '<div class="post-actions">';
            echo '<a href="/post.php?id=' . $post['id'] . '" class="action-btn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Редактировать</a>';
            echo '<a href="/post.php?delete=' . $post['id'] . '" class="action-btn delete" onclick="return confirm(\'Удалить статью?\')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Удалить</a>';
            echo '</div>';
            echo '</article>';
        }
        echo '</div>';
    }

    public function renderSingle(array $post): void
    {
    }

    public function renderForm(array $post = null): void
    {
    }
}
<?php
require_once __DIR__ . '/src/Auth.php';
requireAuth();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Написать статью - TechBlog</title>
    <link rel="stylesheet" href="form.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <a href="/blog/" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            На главную
        </a>
        $controller = new PostController($model);

        if (isset($_GET['delete'])) {
            $controller->delete($_GET['delete']);
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = [
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? '',
                'category' => $_POST['category'] ?? ''
            ];

            if (isset($_GET['id'])) {
                $controller->update($_GET['id'], $post);
            } else {
                $controller->store($post);
            }
            header('Location: /');
            exit;
        }

        $isEdit = isset($_GET['id']);
        $post = $isEdit ? $controller->show($_GET['id']) : null;
        
        if ($isEdit && !$post): ?>
            <p style="color: #ef4444; padding: 20px;">Статья не найдена</p>
        <?php else: ?>
        
        <h1 class="page-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            <?= $isEdit ? 'Редактировать статью' : 'Написать статью' ?>
        </h1>
        <p class="page-subtitle"><?= $isEdit ? 'Обновите содержимое вашей статьи' : 'Поделитесь своими знаниями с читателями' ?></p>
        
        <?php if ($isEdit && $post): ?>
        <div class="form-card">
            <h2 class="form-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Текущая статья
            </h2>
            <div class="post-preview">
                <div class="preview-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Предпросмотр
                </div>
                <h3 class="preview-title"><?= htmlspecialchars($post['title']) ?></h3>
                <div class="preview-meta">
                    <span class="preview-category"><?= htmlspecialchars($post['category']) ?></span>
                    <span><?= date('d.m.Y в H:i', strtotime($post['created_at'])) ?></span>
                </div>
                <p class="preview-content"><?= htmlspecialchars($post['content']) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-card">
            <h2 class="form-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <?= $isEdit ? 'Изменить статью' : 'Содержание статьи' ?>
            </h2>
            
            <form method="POST" action="/post.php<?= $isEdit ? '?id=' . $_GET['id'] : '' ?>">
                <div class="form-group">
                    <label for="title">Заголовок</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="Введите заголовок статьи"
                           value="<?= $post ? htmlspecialchars($post['title']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Категория</label>
                    <select id="category" name="category" required>
                        <option value="">Выберите категорию</option>
                        <option value="tech" <?= $post && $post['category'] === 'tech' ? 'selected' : '' ?>>Tech</option>
                        <option value="programming" <?= $post && $post['category'] === 'programming' ? 'selected' : '' ?>>Программирование</option>
                        <option value="news" <?= $post && $post['category'] === 'news' ? 'selected' : '' ?>>Новости</option>
                        <option value="tutorial" <?= $post && $post['category'] === 'tutorial' ? 'selected' : '' ?>>Уроки</option>
                        <option value="reviews" <?= $post && $post['category'] === 'reviews' ? 'selected' : '' ?>>Обзоры</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content">Содержание</label>
                    <textarea id="content" name="content" required 
                              placeholder="Напишите вашу статью здесь..."><?= $post ? htmlspecialchars($post['content']) : '' ?></textarea>
                    <p class="form-hint">Используйте абзацы для лучшего форматирования</p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                        <?= $isEdit ? 'Сохранить изменения' : 'Опубликовать' ?>
                    </button>
                    <a href="/blog/" class="btn btn-secondary">Отмена</a>
                    <?php if ($isEdit): ?>
                    <a href="/post.php?delete=<?= $_GET['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить статью?')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Удалить
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php endif; ?>
    </div>
    
    <footer>
        <div class="container">
            <p>Powered by <span>Go + PHP</span> | REST API Blog</p>
        </div>
    </footer>
</body>
</html>
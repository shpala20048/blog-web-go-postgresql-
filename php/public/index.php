<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechBlog - IT блог</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-inner">
                <div class="logo">
                    <div class="logo-icon">⚡</div>
                    <div class="logo-text">
                        <h1>TechBlog</h1>
                        <span>Блог о технологиях и программировании</span>
                    </div>
                </div>
                <?php
                require_once __DIR__ . '/src/Model/ApiClient.php';
                require_once __DIR__ . '/src/Model/AuthModel.php';
                $api = new ApiClient();
                $authModel = new AuthModel($api);
                $loggedIn = $authModel->isLoggedIn();
                if ($loggedIn): ?>
                    <a href="/blog/post.php" class="add-post-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Написать статью
                    </a>
                    <a href="/blog/auth.php?action=logout" class="add-post-btn" style="background: var(--bg-card);margin-left:12px">
                        Выйти
                    </a>
                <?php else: ?>
                    <a href="/blog/auth.php" class="add-post-btn">
                        Войти
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <main>
        <div class="container">
            <?php
            require_once __DIR__ . '/src/Model/ApiClient.php';
            require_once __DIR__ . '/src/Model/PostModel.php';
            require_once __DIR__ . '/src/Controller/PostController.php';
            require_once __DIR__ . '/src/View/PostView.php';

            $api = new ApiClient();
            $model = new PostModel($api);
            $controller = new PostController($model);
            $view = new PostView();

            $posts = $controller->index();
            $view->renderList($posts);
            ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>Powered by <span>Go + PHP</span> | REST API Blog</p>
        </div>
    </footer>
</body>
</html>
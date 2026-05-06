<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - TechBlog</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --bg-primary: #0f0f1a;
            --bg-secondary: #1a1a2e;
            --bg-card: #252542;
            --accent-cyan: #00d9ff;
            --accent-green: #00ff88;
            --accent-purple: #a855f7;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: rgba(255, 255, 255, 0.08);
            --gradient-main: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
            --gradient-card: linear-gradient(145deg, var(--bg-card), rgba(37, 37, 66, 0.8));
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, sendPasswordResetEmail, sendEmailVerification } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCkd2GfwV1F90VneqM5ghODL8or24pL6pU",
            authDomain: "testsmsactivate-88af1.firebaseapp.com",
            projectId: "testsmsactivate-88af1",
            storageBucket: "testsmsactivate-88af1.firebasestorage.app",
            messagingSenderId: "1020083323234",
            appId: "1:1020083323234:web:64b2b939bd71d12c052c3b",
            measurementId: "G-CDN7MGHGFX"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        window.auth = auth;
        window.signInWithEmailAndPassword = signInWithEmailAndPassword;
        window.sendPasswordResetEmail = sendPasswordResetEmail;
        window.createUserWithEmailAndPassword = createUserWithEmailAndPassword;
        window.sendEmailVerification = sendEmailVerification;
    </script>

    <style>
        .auth-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .auth-card {
            background: var(--gradient-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(20px);
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }

        .auth-subtitle {
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 32px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-secondary);
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: var(--text-primary);
            font-size: 16px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 4px rgba(0, 217, 255, 0.15);
        }

        .form-input.error {
            border-color: #ff4757;
        }

        .btn {
            width: 100%;
            padding: 16px 24px;
            background: var(--gradient-main);
            border: none;
            border-radius: 14px;
            color: #0f0f1a;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 217, 255, 0.25);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-link {
            background: none;
            border: none;
            color: var(--accent-cyan);
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            width: auto;
            display: inline;
        }

        .btn-link:hover {
            transform: none;
            box-shadow: none;
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: #ff6b7a;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: #00ff88;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        .auth-steps {
            display: none;
        }

        .auth-steps.active {
            display: block;
        }

        #register-section {
            display: none;
        }

        #register-section.active {
            display: block;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--text-muted);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            padding: 0 16px;
        }

        .account-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .account-link a {
            color: var(--accent-cyan);
            text-decoration: none;
        }

        .account-link a:hover {
            text-decoration: underline;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(0, 0, 0, 0.3);
            border-top-color: #0f0f1a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php
    require_once __DIR__ . '/src/Model/ApiClient.php';
    require_once __DIR__ . '/src/Model/AuthModel.php';

    $api = new ApiClient();
    $authModel = new AuthModel($api);

    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        $token = $authModel->getToken();
        if ($token) {
            $authModel->logout($token);
        }
        $authModel->logoutSession();
        header('Location: /blog/auth.php');
        exit;
    }

    $loggedIn = $authModel->isLoggedIn();

    if ($loggedIn) {
        header('Location: /blog/');
        exit;
    }
    ?>

    <header>
        <div class="container">
            <div class="header-inner">
                <div class="logo" onclick="location.href='/blog/'" style="cursor:pointer">
                    <div class="logo-icon">⚡</div>
                    <div class="logo-text">
                        <h1>TechBlog</h1>
                        <span>Блог о технологиях</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-card">
                <div id="login-form" class="auth-steps active">
                    <div class="auth-title">Вход</div>
                    <div class="auth-subtitle">Войдите через email и пароль</div>

                    <div class="error-message" id="error-login"></div>

                    <form id="form-login" method="POST">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-input" placeholder="example@mail.ru" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn" id="btn-login">Войти</button>
                    </form>

                    <div class="account-link">
                        <a href="#" id="forgot-password">Забыли пароль?</a>
                    </div>

                    <div class="account-link">
                        Нет аккаунта? <a href="#" id="register-link">Регистрация</a>
                    </div>

                    <div id="register-section" class="auth-steps">
                        <div class="auth-title">Регистрация</div>
                        <div class="auth-subtitle">Создайте аккаунт</div>
                        <div class="error-message" id="error-register"></div>
                        <form id="form-register">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="reg-email" id="reg-email" class="form-input" placeholder="example@mail.ru" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Пароль</label>
                                <input type="password" name="reg-password" id="reg-password" class="form-input" placeholder="Минимум 6 символов" required minlength="6">
                            </div>
                            <button type="submit" class="btn" id="btn-register">Создать аккаунт</button>
                        </form>
                        <div class="account-link">
                            <a href="#" id="back-to-login-from-reg">Уже есть аккаунт? Войти</a>
                        </div>
                    </div>
                </div>

                <div id="reset-form" class="auth-steps">
                    <div class="auth-title">Сброс пароля</div>
                    <div class="auth-subtitle">Введите email для сброса пароля</div>

                    <div class="error-message" id="error-reset"></div>
                    <div class="success-message" id="success-reset"></div>

                    <form id="form-reset" method="POST">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="reset-email" id="reset-email" class="form-input" placeholder="example@mail.ru" required>
                        </div>
                        <button type="submit" class="btn" id="btn-reset">Отправить ссылку</button>
                    </form>

                    <div class="account-link">
                        <a href="#" id="back-to-login">Вернуться к входу</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Powered by <span>Go + PHP</span> | REST API Blog</p>
        </div>
    </footer>

    <script>
        const authModel = {
            apiUrl: 'http://localhost:8080',
            token: null,

            async login(idToken) {
                const response = await fetch(this.apiUrl + '/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_token: idToken })
                });
                return await response.json();
            },

            async logout() {
                if (!this.token) return { success: false };
                const response = await fetch(this.apiUrl + '/auth/logout', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + this.token
                    },
                    body: JSON.stringify({ token: this.token })
                });
                this.token = null;
                return await response.json();
            },

            async resetPassword(email) {
                const response = await fetch(this.apiUrl + '/auth/reset-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email })
                });
                return await response.json();
            },

            saveToken(token) {
                this.token = token;
                fetch(this.apiUrl + '/auth/verify?token=' + encodeURIComponent(token))
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            sessionStorage.setItem('auth_token', token);
                        }
                    });
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            initLoginForm();
            initResetForm();
            initForgotPassword();
            initRegisterForm();
        });

        function initLoginForm() {
            const form = document.getElementById('form-login');
            const btn = document.getElementById('btn-login');
            const errorEl = document.getElementById('error-login');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                errorEl.classList.remove('show');

                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span>Вход...';

                try {
                    const cred = await window.signInWithEmailAndPassword(window.auth, email, password);
                    const idToken = await cred.user.getIdToken();

                    const result = await authModel.login(idToken);

                    if (result.success) {
                        authModel.saveToken(result.token);
                        location.href = '/blog/';
                    } else {
                        errorEl.textContent = result.message || 'Ошибка входа';
                        errorEl.classList.add('show');
                    }
                } catch (err) {
                    console.error(err);
                    let message = 'Ошибка входа';
                    if (err.code === 'auth/invalid-email') {
                        message = 'Неверный формат email';
                    } else if (err.code === 'auth/user-not-found') {
                        message = 'Пользователь не найден';
                    } else if (err.code === 'auth/wrong-password') {
                        message = 'Неверный пароль';
                    } else if (err.code === 'auth/invalid-credential') {
                        message = 'Неверный email или пароль';
                    } else if (err.code === 'auth/popup-closed-by-user') {
                        message = 'Окно закрыто';
                    }
                    errorEl.textContent = message;
                    errorEl.classList.add('show');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Войти';
                }
            });
        }

        function initForgotPassword() {
            const forgotLink = document.getElementById('forgot-password');
            const backLink = document.getElementById('back-to-login');
            const loginForm = document.getElementById('login-form');
            const resetForm = document.getElementById('reset-form');

            forgotLink.addEventListener('click', function(e) {
                e.preventDefault();
                loginForm.classList.remove('active');
                resetForm.classList.add('active');
            });

            backLink.addEventListener('click', function(e) {
                e.preventDefault();
                resetForm.classList.remove('active');
                loginForm.classList.add('active');
            });
        }

        function initResetForm() {
            const form = document.getElementById('form-reset');
            const btn = document.getElementById('btn-reset');
            const errorEl = document.getElementById('error-reset');
            const successEl = document.getElementById('success-reset');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                errorEl.classList.remove('show');
                successEl.classList.remove('show');

                const email = document.getElementById('reset-email').value;

                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span>Отправка...';

                try {
                    await window.sendPasswordResetEmail(window.auth, email);

                    const result = await authModel.resetPassword(email);
                    successEl.textContent = result.message || 'Ссылка для сброса отправлена на email';
                    successEl.classList.add('show');
                } catch (err) {
                    console.error(err);
                    errorEl.textContent = 'Не удалось отправить ссылку';
                    errorEl.classList.add('show');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Отправить ссылку';
                }
            });
        }

function initRegisterForm() {
            document.addEventListener('DOMContentLoaded', function() {
                const registerLink = document.getElementById('register-link');
                const backToLogin = document.getElementById('back-to-login-from-reg');
                const registerSection = document.getElementById('register-section');
                const form = document.getElementById('form-register');
                const btn = document.getElementById('btn-register');
                const errorEl = document.getElementById('error-register');

                if (!registerLink || !form) return;

                registerLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    registerSection.classList.add('active');
                });

                backToLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    registerSection.classList.remove('active');
                });

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    errorEl.classList.remove('show');

                    const email = document.getElementById('reg-email').value;
                    const password = document.getElementById('reg-password').value;

                    btn.disabled = true;
                    btn.innerHTML = '<span class="loading"></span>Создание...';

                    try {
                        const cred = await window.createUserWithEmailAndPassword(window.auth, email, password);
                        await window.sendEmailVerification(window.auth, cred.user);

                        alert('Регистрация successful! Проверьте email для подтверждения.');
                        registerSection.style.display = 'none';
                    } catch (err) {
                        console.error(err);
                        let message = 'Ошибка регистрации';
                        if (err.code === 'auth/email-already-in-use') {
                            message = 'Email уже зарегистрирован';
                        } else if (err.code === 'auth/invalid-email') {
                            message = 'Неверный email';
                        } else if (err.code === 'auth/weak-password') {
                            message = 'Слабый пароль';
                        }
                        errorEl.textContent = message;
                        errorEl.classList.add('show');
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = 'Создать аккаунт';
                    }
                });
            });
        }
                    errorEl.textContent = message;
                    errorEl.classList.add('show');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Создать аккаунт';
                }
            });
        }
    </script>
</body>
</html>
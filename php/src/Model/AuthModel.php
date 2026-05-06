<?php

require_once __DIR__ . '/ApiClient.php';

class AuthModel
{
    private ApiClient $api;

    public function __construct(ApiClient $api)
    {
        $this->api = $api;
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $idToken): array
    {
        return $this->api->post('/auth/login', ['id_token' => $idToken]);
    }

    public function logout(string $token): array
    {
        return $this->api->post('/auth/logout', ['token' => $token]);
    }

    public function verify(string $token): array
    {
        return $this->api->get('/auth/verify?token=' . urlencode($token));
    }

    public function resetPassword(string $email): array
    {
        return $this->api->post('/auth/reset-password', ['email' => $email]);
    }

    public function getMe(string $token): array
    {
        return $this->api->getWithAuth('/auth/me', $token);
    }

    public function saveToken(string $token): void
    {
        $this->initSession();
        $_SESSION['auth_token'] = $token;
    }

    public function getToken(): ?string
    {
        $this->initSession();
        return $_SESSION['auth_token'] ?? null;
    }

    public function isLoggedIn(): bool
    {
        $token = $this->getToken();
        if (!$token) return false;

        $result = $this->verify($token);
        return $result && ($result['success'] ?? false);
    }

    public function logoutSession(): void
    {
        $this->initSession();
        unset($_SESSION['auth_token']);
    }
}
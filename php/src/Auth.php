<?php

function requireAuth() {
    require_once __DIR__ . '/ApiClient.php';
    require_once __DIR__ . '/AuthModel.php';
    
    $api = new ApiClient();
    $authModel = new AuthModel($api);
    
    if (!$authModel->isLoggedIn()) {
        header('Location: /blog/auth.php');
        exit;
    }
    
    return $authModel;
}

function getCurrentUser() {
    require_once __DIR__ . '/ApiClient.php';
    require_once __DIR__ . '/AuthModel.php';
    
    $api = new ApiClient();
    $authModel = new AuthModel($api);
    $token = $authModel->getToken();
    
    if (!$token) {
        return null;
    }
    
    $result = $api->getWithToken('/auth/me', $token);
    return $result ?: null;
}
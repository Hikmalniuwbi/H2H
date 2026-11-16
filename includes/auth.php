<?php
// Session & Auth Helper untuk Hearts2Hearts

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user']) && !empty($_SESSION['user']['user_id']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool {
    return isLoggedIn() && (($_SESSION['user']['role'] ?? '') === 'admin');
}

function requireLogin(string $redirect = ''): void {
    if (!isLoggedIn()) {
        $target = 'login.php';
        if ($redirect !== '') {
            $target .= '?redirect=' . urlencode($redirect);
        }
        header("Location: $target");
        exit;
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: login.php?error=unauthorized');
        exit;
    }
}

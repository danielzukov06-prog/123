<?php
require_once __DIR__.'/config.php';

function current_user() { return $_SESSION['user'] ?? null; }
function is_logged_in(): bool { return !!current_user(); }
function is_admin(): bool { return (current_user()['is_admin'] ?? 0) == 1; }

function require_login() {
    if(!is_logged_in()) { header('Location: '.BASE_URL.'/login.php'); exit; }
}
function require_admin() {
    require_login(); if(!is_admin()){ http_response_code(403); exit('Forbidden'); }
}

function login(string $email, string $password): bool {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if($u && isset($u['password_hash']) && password_verify($password,$u['password_hash'])){
        $_SESSION['user'] = $u;
        return true;
    }
    return false;
}

function logout() { $_SESSION=[]; session_destroy(); }
?>

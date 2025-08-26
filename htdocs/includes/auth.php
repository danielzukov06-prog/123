<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is authenticated.
 *
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication for a page.
 * Redirects to login page if not authenticated.
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /login.php');
        exit();
    }
}

/**
 * Log in the user by setting session variables.
 *
 * @param int $userId
 */
function loginUser($userId) {
    $_SESSION['user_id'] = $userId;
}

/**
 * Log out the user by destroying the session.
 */
function logoutUser() {
    session_unset();
    session_destroy();
}
?>
<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function e($v)     { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function money($v) { return '₱' . number_format((float)$v, 2); }

function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isAdmin()     { return isLoggedIn() && in_array($_SESSION['role'], ['admin','staff'], true); }
function requireLogin(){ if (!isLoggedIn()) { header('Location: ' . BASE_URL . 'login.php'); exit; } }
function requireAdmin(){ requireLogin(); if (!isAdmin()) { header('Location: ' . BASE_URL . 'index.php'); exit; } }

function flash($key, $msg = null) {
    if ($msg === null) {
        $m = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    $_SESSION['flash'][$key] = $msg;
}

function redirect($url) { header("Location: $url"); exit; }
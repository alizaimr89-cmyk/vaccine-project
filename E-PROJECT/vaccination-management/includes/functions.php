<?php
// Shared authentication, escaping, redirect, and CSRF helpers.
require_once __DIR__ . '/../config/database.php';
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . BASE_URL . $path); exit; }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(?string $role = null): void {
    $user = current_user();
    if (!$user) { redirect('/login.php'); }
    if ($role && $user['role'] !== $role) { http_response_code(403); exit('403 - You do not have permission to access this page.'); }
}
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Invalid form token. Please go back and try again.'); } }
function flash(string $message, string $type='success'): void { $_SESSION['flash'] = ['message'=>$message, 'type'=>$type]; }
function show_flash(): void { if (!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; echo '<div class="alert '.e($f['type']).'">'.e($f['message']).'</div>'; unset($_SESSION['flash']); } }
function role_home(string $role): string { return match($role) { 'admin'=>'/admin/index.php', 'parent'=>'/parent/index.php', 'hospital'=>'/hospital/index.php', default=>'/index.php' }; }

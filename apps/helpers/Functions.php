<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    if (strpos($path, '/POS/public/') === 0) {
        $path = str_replace('/POS/public/', '', $path);
    }
    if (strpos($path, '/POS/') === 0) {
        $path = str_replace('/POS/', '', $path);
    }
    header('Location: ' . $path);
    exit;
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function isLoggedIn() {
    return !empty($_SESSION['user']);
}

function currentRole() {
    return $_SESSION['user']['role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/POS/public/index.php?route=login');
    }
}

function requireRole(array $roles) {
    requireLogin();
    $role = currentRole();
    if (!in_array($role, $roles, true)) {
        redirect('/POS/public/index.php?route=dashboard');
    }
}

function roleLabel($role) {
    $labels = [
        'admin' => 'Admin',
        'supervisor' => 'Supervisor',
        'cashier' => 'Cashier',
        'waiter' => 'Waiter',
        'reception' => 'Reception'
    ];
    return $labels[$role] ?? ucfirst($role);
}

function formatCurrency($value) {
    return '$' . number_format((float)$value, 2);
}

function resolveApiUser() {
    if (isLoggedIn()) {
        return currentUser();
    }
    $token = $_SERVER['HTTP_X_DEVICE_TOKEN'] ?? ($_POST['device_token'] ?? null);
    if (!$token) {
        return null;
    }
    return DeviceToken::resolveUser($token);
}

function requireApiRole(array $roles) {
    $user = resolveApiUser();
    if (!$user) {
        apiError('Not authenticated.', 401);
    }
    if (!in_array($user['role'] ?? '', $roles, true)) {
        apiError('Not authorized.', 403);
    }
    return $user;
}

function apiRespond($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function apiError(string $message, int $status = 400): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function sanitizeUser(array $user): array {
    unset($user['password'], $user['pin']);
    return $user;
}

function isFloorOpsRole(?string $role): bool {
    return in_array($role, ['waiter', 'cashier', 'reception'], true);
}

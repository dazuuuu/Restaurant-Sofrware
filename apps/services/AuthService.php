<?php
class AuthService {
    public static function adminLogin(string $email, string $password): ?array {
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'] ?? '')) {
            return null;
        }
        if (($user['role'] ?? '') !== 'admin' && ($user['role'] ?? '') !== 'supervisor') {
            return null;
        }
        if (($user['status'] ?? 'active') !== 'active') {
            return null;
        }
        $_SESSION['user'] = $user;
        return $user;
    }

    public static function staffLogin(int $userId, string $pin): ?array {
        $user = User::findById($userId);
        if (!$user || ($user['pin'] ?? '') !== $pin) {
            return null;
        }
        if (($user['status'] ?? 'active') !== 'active') {
            return null;
        }
        if (($user['role'] ?? '') === 'admin' || ($user['role'] ?? '') === 'supervisor') {
            return null;
        }
        $_SESSION['user'] = $user;
        return $user;
    }

    public static function logout(): void {
        unset($_SESSION['user']);
    }
}

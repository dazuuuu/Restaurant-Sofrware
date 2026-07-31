<?php
class AuthController {
    public function loginAdmin(): void {
        if (isLoggedIn() && in_array(currentRole(), ['admin', 'supervisor'], true)) {
            redirect('index.php?route=dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $user = AuthService::adminLogin($email, $password);
            if ($user) {
                redirect('index.php?route=dashboard');
            }
            $_SESSION['error'] = 'Invalid email or password.';
        }
        include APP_ROOT . '/apps/views/auth/login-admin.php';
    }

    public function loginStaff(): void {
        if (isLoggedIn() && !in_array(currentRole(), ['admin', 'supervisor'], true)) {
            redirect('index.php?route=dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)trim($_POST['user_id'] ?? 0);
            $pin = trim($_POST['pin'] ?? '');
            $user = AuthService::staffLogin($userId, $pin);
            if ($user) {
                redirect('index.php?route=dashboard');
            }
            $_SESSION['error'] = 'Invalid User ID or PIN.';
        }
        
        $staffUsers = Database::getConnection()->query('SELECT id, full_name, role FROM users WHERE role NOT IN ("admin", "supervisor") AND status = "active" ORDER BY full_name')->fetchAll();
        include APP_ROOT . '/apps/views/auth/login-staff.php';
    }

    public function logout(): void {
        AuthService::logout();
        redirect('index.php?route=login');
    }
}

<?php
class PasswordResetController {
    public function forgotPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Enter a valid email address.';
                include APP_ROOT . '/apps/views/auth/forgot-password.php';
                return;
            }

            PasswordResetService::requestReset($email);
            $_SESSION['reset_email'] = $email;
            $_SESSION['info'] = 'If that email is registered, a 6-digit code has been sent. It expires in 10 minutes.';
            redirect('index.php?route=reset-password');
            return;
        }

        include APP_ROOT . '/apps/views/auth/forgot-password.php';
    }

    public function resetPassword(): void {
        $email = $_SESSION['reset_email'] ?? '';
        if ($email === '') {
            redirect('index.php?route=forgot-password');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($newPassword) < 8) {
                $_SESSION['error'] = 'Password must be at least 8 characters.';
                include APP_ROOT . '/apps/views/auth/reset-password.php';
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match.';
                include APP_ROOT . '/apps/views/auth/reset-password.php';
                return;
            }

            $result = PasswordResetService::verifyAndReset($email, $otp, $newPassword);
            if (!$result['success']) {
                $_SESSION['error'] = $result['error'];
                include APP_ROOT . '/apps/views/auth/reset-password.php';
                return;
            }

            unset($_SESSION['reset_email']);
            $_SESSION['info'] = 'Password reset successfully. You can now log in with your new password.';
            redirect('index.php?route=login-admin');
            return;
        }

        include APP_ROOT . '/apps/views/auth/reset-password.php';
    }
}

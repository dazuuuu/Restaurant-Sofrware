<?php
class PasswordResetService {
    private const OTP_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;

    public static function requestReset(string $email): void {
        $user = User::findByEmail($email);
        if (!$user || !in_array($user['role'] ?? '', ['admin', 'supervisor'], true)) {
            return;
        }

        $db = Database::getConnection();

        $recent = $db->prepare('SELECT created_at FROM password_resets WHERE email = ? ORDER BY id DESC LIMIT 1');
        $recent->execute([$email]);
        $last = $recent->fetch();
        if ($last && (time() - strtotime($last['created_at'])) < self::RESEND_COOLDOWN_SECONDS) {
            return;
        }

        $db->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', time() + self::OTP_TTL_MINUTES * 60);

        $stmt = $db->prepare('INSERT INTO password_resets (email, otp_hash, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$email, password_hash($otp, PASSWORD_BCRYPT), $expiresAt]);

        MailService::sendOtp($email, $user['full_name'], $otp);
    }

    public static function verifyAndReset(string $email, string $otp, string $newPassword): array {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE email = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$email]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return ['success' => false, 'error' => 'No reset code was requested for this email.'];
        }

        if ($reset['attempts'] >= self::MAX_ATTEMPTS) {
            $db->prepare('DELETE FROM password_resets WHERE id = ?')->execute([$reset['id']]);
            return ['success' => false, 'error' => 'Too many incorrect attempts. Please request a new code.'];
        }

        if (strtotime($reset['expires_at']) < time()) {
            $db->prepare('DELETE FROM password_resets WHERE id = ?')->execute([$reset['id']]);
            return ['success' => false, 'error' => 'This code has expired. Please request a new one.'];
        }

        if (!password_verify($otp, $reset['otp_hash'])) {
            $db->prepare('UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?')->execute([$reset['id']]);
            return ['success' => false, 'error' => 'Incorrect code.'];
        }

        $user = User::findByEmail($email);
        if (!$user) {
            return ['success' => false, 'error' => 'Account no longer exists.'];
        }

        User::updatePassword((int)$user['id'], $newPassword);
        $db->prepare('DELETE FROM password_resets WHERE id = ?')->execute([$reset['id']]);

        MailService::sendResetConfirmation($email, $user['full_name']);

        return ['success' => true, 'error' => null];
    }
}

<?php
class DeviceToken {
    public static function issue(int $userId): string {
        $plainToken = bin2hex(random_bytes(32));
        $stmt = Database::getConnection()->prepare('INSERT INTO device_tokens (user_id, token_hash) VALUES (?, ?)');
        $stmt->execute([$userId, hash('sha256', $plainToken)]);
        return $plainToken;
    }

    public static function resolveUser(string $plainToken): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM device_tokens WHERE token_hash = ? AND revoked = 0');
        $stmt->execute([hash('sha256', $plainToken)]);
        $tokenRow = $stmt->fetch();
        if (!$tokenRow) {
            return null;
        }

        $user = User::findById((int)$tokenRow['user_id']);
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            return null;
        }

        Database::getConnection()
            ->prepare('UPDATE device_tokens SET last_seen_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$tokenRow['id']]);

        return $user;
    }
}

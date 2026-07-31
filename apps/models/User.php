<?php
class User {
    public static function findByEmail(string $email): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function create(array $data): array {
        $stmt = Database::getConnection()->prepare('INSERT INTO users (full_name, email, password, pin, role, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            !empty($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
            !empty($data['pin']) ? $data['pin'] : null,
            $data['role'],
            $data['status'] ?? 'active'
        ]);
        return self::findById((int)Database::getConnection()->lastInsertId());
    }

    public static function update(int $id, array $data): array {
        $current = self::findById($id);
        $password = $current['password'];
        $pin = $current['pin'];
        
        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (!empty($data['pin'])) {
            $pin = $data['pin'];
        }
        
        $stmt = Database::getConnection()->prepare('UPDATE users SET full_name = ?, email = ?, password = ?, pin = ?, role = ?, status = ? WHERE id = ?');
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $password,
            $pin,
            $data['role'],
            $data['status'] ?? 'active',
            $id
        ]);
        return self::findById($id);
    }

    public static function updatePassword(int $id, string $plainPassword): void {
        $stmt = Database::getConnection()->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([password_hash($plainPassword, PASSWORD_BCRYPT), $id]);
    }

    public static function delete(int $id): void {
        $stmt = Database::getConnection()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }
}

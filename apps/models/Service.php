<?php
class Service {
    public static function all(): array {
        $stmt = Database::getConnection()->query('SELECT * FROM services ORDER BY category, name');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM services WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): array {
        $stmt = Database::getConnection()->prepare('INSERT INTO services (name, category, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['category'],
            $data['description'],
            (float)$data['price'],
            $data['image'] ?? '',
            $data['status'] ?? 'active'
        ]);
        return self::findById((int)Database::getConnection()->lastInsertId());
    }

    public static function update(int $id, array $data): array {
        $stmt = Database::getConnection()->prepare('UPDATE services SET name = ?, category = ?, description = ?, price = ?, image = ?, status = ? WHERE id = ?');
        $stmt->execute([
            $data['name'],
            $data['category'],
            $data['description'],
            (float)$data['price'],
            $data['image'] ?? '',
            $data['status'] ?? 'active',
            $id
        ]);
        return self::findById($id);
    }

    public static function delete(int $id): void {
        $stmt = Database::getConnection()->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([$id]);
    }
}

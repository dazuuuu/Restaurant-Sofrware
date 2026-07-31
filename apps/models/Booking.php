<?php
class Booking {
    public static function all(): array {
        $stmt = Database::getConnection()->query('SELECT * FROM bookings ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findByClientId(string $clientId): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM bookings WHERE client_id = ?');
        $stmt->execute([$clientId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): array {
        if (!empty($data['client_id'])) {
            $existing = self::findByClientId($data['client_id']);
            if ($existing) {
                return $existing;
            }
        }

        $code = 'BK' . strtoupper(substr(md5(uniqid('', true)), 0, 6));
        $stmt = Database::getConnection()->prepare('INSERT INTO bookings (booking_code, client_id, customer_name, contact, event_type, table_name, deposit, food_items, service_items, attendees, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $code,
            $data['client_id'] ?? null,
            $data['customer_name'],
            $data['contact'],
            $data['event_type'],
            $data['table_name'] ?? '',
            (float)($data['deposit'] ?? 0),
            $data['food_items'] ?? '',
            $data['service_items'] ?? '',
            (int)($data['attendees'] ?? 1),
            $data['status'] ?? 'pending',
            $data['created_by'] ?? null
        ]);
        return self::findById((int)Database::getConnection()->lastInsertId());
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function update(int $id, array $data): array {
        $stmt = Database::getConnection()->prepare('UPDATE bookings SET status = ?, deposit = ?, table_name = ?, food_items = ?, service_items = ?, attendees = ?, customer_name = ?, contact = ?, event_type = ? WHERE id = ?');
        $stmt->execute([
            $data['status'] ?? 'pending',
            (float)($data['deposit'] ?? 0),
            $data['table_name'] ?? '',
            $data['food_items'] ?? '',
            $data['service_items'] ?? '',
            (int)($data['attendees'] ?? 1),
            $data['customer_name'],
            $data['contact'],
            $data['event_type'],
            $id
        ]);
        return self::findById($id);
    }
}

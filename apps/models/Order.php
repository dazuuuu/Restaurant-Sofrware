<?php
class Order {
    public static function all(): array {
        $stmt = Database::getConnection()->query('SELECT * FROM orders ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByCode(string $code): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM orders WHERE order_code = ?');
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public static function findByClientId(string $clientId): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM orders WHERE client_id = ?');
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

        $code = 'ORD' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        $stmt = Database::getConnection()->prepare('INSERT INTO orders (order_code, client_id, table_number, customer_name, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $code,
            $data['client_id'] ?? null,
            $data['table_number'],
            $data['customer_name'] ?? '',
            $data['status'] ?? 'pending',
            $data['notes'] ?? '',
            $data['created_by'] ?? null
        ]);
        return self::findById((int)Database::getConnection()->lastInsertId());
    }

    public static function update(int $id, array $data): array {
        $stmt = Database::getConnection()->prepare('UPDATE orders SET table_number = ?, customer_name = ?, total_amount = ?, payment_method = ?, status = ?, notes = ? WHERE id = ?');
        $stmt->execute([
            $data['table_number'],
            $data['customer_name'] ?? '',
            (float)($data['total_amount'] ?? 0),
            $data['payment_method'] ?? '',
            $data['status'] ?? 'pending',
            $data['notes'] ?? '',
            $id
        ]);
        return self::findById($id);
    }

    public static function updateStatus(int $id, string $status): void {
        $stmt = Database::getConnection()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public static function completeOrder(int $id): void {
        $stmt = Database::getConnection()->prepare('UPDATE orders SET status = ?, completed_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute(['completed', $id]);
    }

    public static function getOrderItems(int $orderId): array {
        $stmt = Database::getConnection()->prepare('SELECT oi.*, mi.name, mi.category FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function addItem(int $orderId, int $menuItemId, int $quantity, string $instructions = '', ?string $clientId = null): array {
        if ($clientId) {
            $stmt = Database::getConnection()->prepare('SELECT id FROM order_items WHERE client_id = ?');
            $stmt->execute([$clientId]);
            if ($stmt->fetch()) {
                return self::getOrderItems($orderId);
            }
        }

        $item = \MenuItem::findById($menuItemId);
        $subtotal = ($item['price'] ?? 0) * $quantity;

        $stmt = Database::getConnection()->prepare('INSERT INTO order_items (order_id, client_id, menu_item_id, quantity, unit_price, subtotal, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$orderId, $clientId, $menuItemId, $quantity, $item['price'] ?? 0, $subtotal, $instructions]);

        self::updateTotal($orderId);
        return self::getOrderItems($orderId);
    }

    public static function updateTotal(int $orderId): void {
        $stmt = Database::getConnection()->prepare('SELECT SUM(subtotal) as total FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        $result = $stmt->fetch();
        $total = $result['total'] ?? 0;

        $updateStmt = Database::getConnection()->prepare('UPDATE orders SET total_amount = ? WHERE id = ?');
        $updateStmt->execute([$total, $orderId]);
    }

    public static function deleteItem(int $itemId): void {
        $stmt = Database::getConnection()->prepare('SELECT order_id FROM order_items WHERE id = ?');
        $stmt->execute([$itemId]);
        $result = $stmt->fetch();
        
        $deleteStmt = Database::getConnection()->prepare('DELETE FROM order_items WHERE id = ?');
        $deleteStmt->execute([$itemId]);
        
        if ($result['order_id']) {
            self::updateTotal($result['order_id']);
        }
    }
}

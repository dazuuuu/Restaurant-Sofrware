<?php
class Receipt {
    public static function create(int $orderId, string $type = 'customer'): array {
        $code = 'RCP' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        $order = Order::findById($orderId);
        $items = Order::getOrderItems($orderId);
        
        $content = self::generateReceiptContent($order, $items);
        
        $stmt = Database::getConnection()->prepare('INSERT INTO receipts (receipt_code, order_id, receipt_type, content) VALUES (?, ?, ?, ?)');
        $stmt->execute([$code, $orderId, $type, $content]);
        
        return self::findById((int)Database::getConnection()->lastInsertId());
    }

    public static function findById(int $id): ?array {
        $stmt = Database::getConnection()->prepare('SELECT * FROM receipts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function getByOrder(int $orderId, string $type = ''): array {
        if ($type) {
            $stmt = Database::getConnection()->prepare('SELECT * FROM receipts WHERE order_id = ? AND receipt_type = ? ORDER BY created_at DESC');
            $stmt->execute([$orderId, $type]);
        } else {
            $stmt = Database::getConnection()->prepare('SELECT * FROM receipts WHERE order_id = ? ORDER BY created_at DESC');
            $stmt->execute([$orderId]);
        }
        return $stmt->fetchAll();
    }

    private static function generateReceiptContent(array $order, array $items): string {
        $settings = Settings::all();
        $symbol = $settings['currency_symbol'] ?? 'Ksh';
        $restaurant = $settings['restaurant_name'] ?? 'My Restaurant';
        
        $content = "\n";
        $content .= "==========================================\n";
        $content .= "       " . strtoupper($restaurant) . "\n";
        $content .= "==========================================\n\n";
        $content .= "Receipt #: " . $order['order_code'] . "\n";
        $content .= "Date: " . date('d/m/Y H:i:s') . "\n";
        $content .= "Table: " . $order['table_number'] . "\n";
        $content .= "Customer: " . ($order['customer_name'] ?: 'Walk-in') . "\n\n";
        $content .= "------------------------------------------\n";
        $content .= "ITEMS\n";
        $content .= "------------------------------------------\n";
        
        foreach ($items as $item) {
            $content .= sprintf(
                "%-30s x%d  %s%.2f\n",
                substr($item['name'], 0, 30),
                $item['quantity'],
                $symbol,
                $item['subtotal']
            );
            if ($item['special_instructions']) {
                $content .= "  Note: " . $item['special_instructions'] . "\n";
            }
        }
        
        $content .= "------------------------------------------\n";
        $content .= sprintf("Total Amount: %s%.2f\n\n", $symbol, $order['total_amount']);
        $content .= "Status: " . ucfirst($order['status']) . "\n\n";
        $content .= ($settings['receipt_footer'] ?? "Thank you for your visit!") . "\n";
        $content .= "==========================================\n";
        
        return $content;
    }

    public static function printReceipt(int $receiptId): string {
        $receipt = self::findById($receiptId);
        return $receipt ? $receipt['content'] : '';
    }
}

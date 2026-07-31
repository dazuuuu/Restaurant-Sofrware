<?php
class Settings {
    public static function get(string $key, $default = null) {
        $stmt = Database::getConnection()->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    }

    public static function set(string $key, $value): void {
        $stmt = Database::getConnection()->prepare('INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)');
        $stmt->execute([$key, $value]);
    }

    public static function all(): array {
        $stmt = Database::getConnection()->query('SELECT * FROM settings');
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public static function getCurrencySymbol(): string {
        return self::get('currency_symbol', 'Ksh');
    }

    public static function getRestaurantName(): string {
        return self::get('restaurant_name', 'My Restaurant');
    }

    public static function getLogoUrl(): string {
        return self::get('logo_url', 'https://via.placeholder.com/150');
    }

    public static function getReceiptFooter(): string {
        return self::get('receipt_footer', 'Thank you for your visit!');
    }
}

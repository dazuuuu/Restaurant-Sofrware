<?php
class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            self::$pdo = new PDO('sqlite:' . DB_PATH);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->exec('PRAGMA foreign_keys = ON;');
        }

        return self::$pdo;
    }

    public static function initialize(): void {
        $pdo = self::getConnection();

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT,
                pin TEXT,
                role TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS menu_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL,
                image TEXT,
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL,
                image TEXT,
                status TEXT NOT NULL DEFAULT 'active',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_code TEXT NOT NULL UNIQUE,
                client_id TEXT,
                customer_name TEXT NOT NULL,
                contact TEXT NOT NULL,
                event_type TEXT NOT NULL,
                table_name TEXT,
                deposit REAL NOT NULL DEFAULT 0,
                food_items TEXT,
                service_items TEXT,
                attendees INTEGER NOT NULL DEFAULT 1,
                status TEXT NOT NULL DEFAULT 'pending',
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(created_by) REFERENCES users(id)
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_code TEXT NOT NULL UNIQUE,
                client_id TEXT,
                table_number TEXT NOT NULL,
                customer_name TEXT,
                total_amount REAL NOT NULL DEFAULT 0,
                payment_method TEXT,
                status TEXT NOT NULL DEFAULT 'pending',
                notes TEXT,
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                completed_at TEXT,
                FOREIGN KEY(created_by) REFERENCES users(id)
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                client_id TEXT,
                menu_item_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                unit_price REAL NOT NULL,
                subtotal REAL NOT NULL,
                special_instructions TEXT,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(order_id) REFERENCES orders(id),
                FOREIGN KEY(menu_item_id) REFERENCES menu_items(id)
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS receipts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                receipt_code TEXT NOT NULL UNIQUE,
                order_id INTEGER NOT NULL,
                receipt_type TEXT NOT NULL DEFAULT 'customer',
                content TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(order_id) REFERENCES orders(id)
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                otp_hash TEXT NOT NULL,
                attempts INTEGER NOT NULL DEFAULT 0,
                expires_at TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT,
                updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS device_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token_hash TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                last_seen_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                revoked INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY(user_id) REFERENCES users(id)
            )
        ");

        self::migrateColumn('users', 'pin', 'TEXT');
        self::migrateUsersPasswordNullable();
        self::migrateColumn('orders', 'client_id', 'TEXT');
        self::migrateColumn('order_items', 'client_id', 'TEXT');
        self::migrateColumn('bookings', 'client_id', 'TEXT');

        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_orders_client_id ON orders(client_id) WHERE client_id IS NOT NULL');
        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_order_items_client_id ON order_items(client_id) WHERE client_id IS NOT NULL');
        $pdo->exec('CREATE UNIQUE INDEX IF NOT EXISTS idx_bookings_client_id ON bookings(client_id) WHERE client_id IS NOT NULL');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_device_tokens_token_hash ON device_tokens(token_hash)');

        $pdo->exec("
            INSERT OR IGNORE INTO users (id, full_name, email, password, role, status) VALUES
            (1, 'Dazu Hub Admin', 'dazuhubs@gmail.com', '\$2y\$10\$Z0ZBGdC/aqm9J3f19TUr8ugo18HTv0LwRCCgTP5tCDwQqhP05VHwq', 'admin', 'active')
        ");

        $pdo->exec("
            INSERT OR IGNORE INTO menu_items (id, name, category, description, price, image, status) VALUES
            (1, 'Grilled Chicken', 'Food', 'Served with rice and salad.', 18.50, 'https://images.unsplash.com/photo-1518492104633-130d0cc84637', 'active'),
            (2, 'Vegetable Pasta', 'Food', 'Creamy pasta with fresh vegetables.', 14.00, 'https://images.unsplash.com/photo-1551183053-bf91a1d81141', 'active'),
            (3, 'Fresh Juice', 'Drinks', 'Orange, mango or pineapple.', 6.50, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b', 'active')
        ");

        $pdo->exec("
            INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES
            ('currency', 'KSH'),
            ('currency_symbol', 'Ksh'),
            ('restaurant_name', 'My Restaurant'),
            ('logo_url', 'https://via.placeholder.com/150'),
            ('receipt_footer', 'Thank you for your visit!')
        ");
    }

    private static function migrateUsersPasswordNullable(): void {
        $pdo = self::getConnection();
        $columns = $pdo->query('PRAGMA table_info(users)')->fetchAll();
        $needsFix = false;
        foreach ($columns as $col) {
            if ($col['name'] === 'password' && (int)$col['notnull'] === 1) {
                $needsFix = true;
                break;
            }
        }
        if (!$needsFix) {
            return;
        }

        $pdo->exec('PRAGMA foreign_keys=OFF');
        $pdo->beginTransaction();
        try {
            $pdo->exec("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    full_name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT,
                    pin TEXT,
                    role TEXT NOT NULL,
                    status TEXT NOT NULL DEFAULT 'active',
                    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
                )
            ");
            $pdo->exec('INSERT INTO users_new (id, full_name, email, password, pin, role, status, created_at)
                        SELECT id, full_name, email, password, pin, role, status, created_at FROM users');
            $pdo->exec('DROP TABLE users');
            $pdo->exec('ALTER TABLE users_new RENAME TO users');
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            $pdo->exec('PRAGMA foreign_keys=ON');
            throw $e;
        }
        $pdo->exec('PRAGMA foreign_keys=ON');
    }

    private static function migrateColumn(string $table, string $column, string $definition): void {
        $pdo = self::getConnection();
        $columns = $pdo->query("PRAGMA table_info($table)")->fetchAll();
        foreach ($columns as $col) {
            if ($col['name'] === $column) {
                return;
            }
        }
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}

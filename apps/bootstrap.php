<?php
session_start();

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', APP_ROOT . '/storage');
}

if (!defined('DB_PATH')) {
    define('DB_PATH', STORAGE_PATH . '/pos.sqlite');
}

if (!is_dir(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0777, true);
}

require_once APP_ROOT . '/vendor/autoload.php';

require_once APP_ROOT . '/apps/helpers/Functions.php';
require_once APP_ROOT . '/apps/models/Database.php';
require_once APP_ROOT . '/apps/models/User.php';
require_once APP_ROOT . '/apps/models/MenuItem.php';
require_once APP_ROOT . '/apps/models/Service.php';
require_once APP_ROOT . '/apps/models/Booking.php';
require_once APP_ROOT . '/apps/models/Order.php';
require_once APP_ROOT . '/apps/models/Settings.php';
require_once APP_ROOT . '/apps/models/Receipt.php';
require_once APP_ROOT . '/apps/models/DeviceToken.php';
require_once APP_ROOT . '/apps/services/MailService.php';
require_once APP_ROOT . '/apps/services/PasswordResetService.php';
require_once APP_ROOT . '/apps/services/AuthService.php';
require_once APP_ROOT . '/apps/services/UserService.php';
require_once APP_ROOT . '/apps/services/MenuService.php';
require_once APP_ROOT . '/apps/services/ServiceService.php';
require_once APP_ROOT . '/apps/services/BookingService.php';
require_once APP_ROOT . '/apps/services/OrderService.php';
require_once APP_ROOT . '/apps/services/SettingsService.php';
require_once APP_ROOT . '/apps/controllers/AuthController.php';
require_once APP_ROOT . '/apps/controllers/PasswordResetController.php';
require_once APP_ROOT . '/apps/controllers/api/AuthApiController.php';
require_once APP_ROOT . '/apps/controllers/api/OrderApiController.php';
require_once APP_ROOT . '/apps/controllers/api/BookingApiController.php';
require_once APP_ROOT . '/apps/controllers/api/KitchenApiController.php';
require_once APP_ROOT . '/apps/controllers/DashboardController.php';
require_once APP_ROOT . '/apps/controllers/UserController.php';
require_once APP_ROOT . '/apps/controllers/MenuController.php';
require_once APP_ROOT . '/apps/controllers/ServiceController.php';
require_once APP_ROOT . '/apps/controllers/BookingController.php';
require_once APP_ROOT . '/apps/controllers/OrderController.php';
require_once APP_ROOT . '/apps/controllers/SettingsController.php';
require_once APP_ROOT . '/apps/controllers/KitchenController.php';

Database::initialize();

<?php
require_once dirname(__DIR__) . '/apps/bootstrap.php';

$route = $_GET['route'] ?? 'login';

$authController = new AuthController();
$dashboardController = new DashboardController();
$userController = new UserController();
$menuController = new MenuController();
$serviceController = new ServiceController();
$bookingController = new BookingController();
$orderController = new OrderController();
$settingsController = new SettingsController();
$passwordResetController = new PasswordResetController();
$kitchenController = new KitchenController();

switch ($route) {
    // Auth routes
    case 'login':
    case 'login-admin':
        $authController->loginAdmin();
        break;
    case 'login-staff':
        $authController->loginStaff();
        break;
    case 'logout':
        $authController->logout();
        break;
    case 'forgot-password':
        $passwordResetController->forgotPassword();
        break;
    case 'reset-password':
        $passwordResetController->resetPassword();
        break;

    // Dashboard
    case 'dashboard':
        $dashboardController->index();
        break;

    // User management
    case 'users':
        $userController->index();
        break;
    case 'users-create':
        $userController->create();
        break;
    case 'users-edit':
        $userController->edit();
        break;
    case 'users-delete':
        $userController->delete();
        break;

    // Menu management
    case 'menu':
        $menuController->index();
        break;
    case 'menu-create':
        $menuController->create();
        break;
    case 'menu-edit':
        $menuController->edit();
        break;
    case 'menu-delete':
        $menuController->delete();
        break;

    // Services management
    case 'services':
        $serviceController->index();
        break;
    case 'services-create':
        $serviceController->create();
        break;
    case 'services-edit':
        $serviceController->edit();
        break;
    case 'services-delete':
        $serviceController->delete();
        break;

    // Bookings
    case 'bookings':
        $bookingController->index();
        break;
    case 'bookings-create':
        $bookingController->create();
        break;
    case 'bookings-edit':
        $bookingController->edit();
        break;

    // Orders
    case 'orders':
        $orderController->index();
        break;
    case 'order-create':
        $orderController->create();
        break;
    case 'order-view':
        $orderController->view();
        break;
    case 'order-add-item':
        $orderController->addItem();
        break;
    case 'order-remove-item':
        $orderController->removeItem();
        break;
    case 'print-receipt':
        $orderController->printReceipt();
        break;
    case 'order-complete':
        $orderController->complete();
        break;

    // Settings
    case 'settings':
        $settingsController->index();
        break;
    case 'settings-update':
        $settingsController->update();
        break;

    // Kitchen
    case 'kitchen-dashboard':
        $kitchenController->dashboard();
        break;
    case 'kitchen-view':
        $kitchenController->viewOrder();
        break;
    case 'kitchen-ready':
        $kitchenController->markReady();
        break;

    default:
        redirect('index.php?route=login-admin');
}

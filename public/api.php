<?php
require_once dirname(__DIR__) . '/apps/bootstrap.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'device-login':
            (new AuthApiController())->deviceLogin();
            break;
        case 'device-check':
            (new AuthApiController())->deviceCheck();
            break;

        case 'order-list':
            (new OrderApiController())->list();
            break;
        case 'order-create':
            (new OrderApiController())->create();
            break;
        case 'order-add-item':
            (new OrderApiController())->addItem();
            break;
        case 'order-remove-item':
            (new OrderApiController())->removeItem();
            break;
        case 'order-complete':
            (new OrderApiController())->complete();
            break;

        case 'booking-create':
            (new BookingApiController())->create();
            break;

        case 'kitchen-list':
            (new KitchenApiController())->list();
            break;
        case 'kitchen-mark-ready':
            (new KitchenApiController())->markReady();
            break;

        default:
            apiError('Unknown action.', 404);
    }
} catch (Throwable $e) {
    error_log('API error [' . $action . ']: ' . $e->getMessage());
    apiError('Server error.', 500);
}

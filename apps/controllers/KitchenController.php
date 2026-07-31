<?php
class KitchenController {
    public function dashboard(): void {
        requireRole(['cashier', 'admin', 'supervisor']);
        $orders = Order::all();
        include APP_ROOT . '/apps/views/kitchen/dashboard.php';
    }

    public function viewOrder(): void {
        requireRole(['cashier', 'admin', 'supervisor']);
        $orderId = (int)($_GET['id'] ?? 0);
        $order = Order::findById($orderId);
        if (!$order) {
            redirect('index.php?route=kitchen-dashboard');
        }
        $items = Order::getOrderItems($orderId);
        include APP_ROOT . '/apps/views/kitchen/order-detail.php';
    }

    public function markReady(): void {
        requireRole(['cashier', 'admin', 'supervisor']);
        $orderId = (int)($_GET['id'] ?? 0);
        if ($orderId > 0) {
            Order::updateStatus($orderId, 'ready');
        }
        redirect('index.php?route=kitchen-dashboard');
    }
}

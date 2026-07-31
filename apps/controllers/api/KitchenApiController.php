<?php
class KitchenApiController {
    public function list(): void {
        requireApiRole(['cashier', 'admin', 'supervisor']);
        apiRespond(['orders' => Order::all()]);
    }

    public function markReady(): void {
        requireApiRole(['cashier', 'admin', 'supervisor']);

        $orderId = (int)($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $orderClientId = trim($_POST['order_client_id'] ?? '');
            $order = $orderClientId !== '' ? Order::findByClientId($orderClientId) : null;
            $orderId = $order ? (int)$order['id'] : 0;
        }

        if (!$orderId || !Order::findById($orderId)) {
            apiError('order_id or order_client_id is required and must exist.');
        }

        Order::updateStatus($orderId, 'ready');
        apiRespond(['order' => Order::findById($orderId)]);
    }
}

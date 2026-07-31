<?php
class OrderApiController {
    private function resolveOrderId(): ?int {
        $orderId = (int)($_POST['order_id'] ?? 0);
        if ($orderId > 0 && Order::findById($orderId)) {
            return $orderId;
        }
        $orderClientId = trim($_POST['order_client_id'] ?? '');
        if ($orderClientId !== '') {
            $order = Order::findByClientId($orderClientId);
            if ($order) {
                return (int)$order['id'];
            }
        }
        return null;
    }

    public function list(): void {
        requireApiRole(['waiter', 'cashier', 'admin', 'supervisor']);
        apiRespond(['orders' => Order::all()]);
    }

    public function create(): void {
        $user = requireApiRole(['waiter']);
        $tableNumber = trim($_POST['table_number'] ?? '');
        if ($tableNumber === '') {
            apiError('table_number is required.');
        }

        $order = OrderService::create([
            'client_id' => trim($_POST['client_id'] ?? '') ?: null,
            'table_number' => $tableNumber,
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'created_by' => $user['id'],
        ]);

        apiRespond(['order' => $order]);
    }

    public function addItem(): void {
        requireApiRole(['waiter']);
        $orderId = $this->resolveOrderId();
        $menuItemId = (int)($_POST['menu_item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$orderId || $menuItemId <= 0 || $quantity <= 0) {
            apiError('order_id/order_client_id, menu_item_id and quantity are required.');
        }

        $items = OrderService::addItem(
            $orderId,
            $menuItemId,
            $quantity,
            trim($_POST['instructions'] ?? ''),
            trim($_POST['client_id'] ?? '') ?: null
        );

        apiRespond(['order_id' => $orderId, 'items' => $items, 'order' => Order::findById($orderId)]);
    }

    public function removeItem(): void {
        requireApiRole(['waiter']);
        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId > 0) {
            OrderService::removeItem($itemId);
        }
        apiRespond(['ok' => true]);
    }

    public function complete(): void {
        requireApiRole(['cashier', 'admin', 'supervisor']);
        $orderId = $this->resolveOrderId();
        if (!$orderId) {
            apiError('order_id or order_client_id is required.');
        }

        $order = Order::findById($orderId);
        if ($order['status'] === 'completed') {
            apiRespond(['order' => $order, 'already_completed' => true]);
            return;
        }

        OrderService::complete($orderId);
        apiRespond(['order' => Order::findById($orderId)]);
    }
}

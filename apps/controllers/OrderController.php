<?php
class OrderController {
    public function index(): void {
        requireRole(['waiter', 'cashier', 'admin', 'supervisor']);
        $orders = Order::all();
        include APP_ROOT . '/apps/views/waiter/orders.php';
    }

    public function create(): void {
        requireRole(['waiter']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = OrderService::create([
                'table_number' => trim($_POST['table_number'] ?? ''),
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'created_by' => currentUser()['id'] ?? null
            ]);
            redirect('index.php?route=order-view&id=' . $order['id']);
        }
        include APP_ROOT . '/apps/views/waiter/order-create.php';
    }

    public function view(): void {
        requireRole(['waiter', 'cashier', 'admin', 'supervisor']);
        $orderId = (int)($_GET['id'] ?? 0);
        $order = Order::findById($orderId);
        if (!$order) {
            redirect('index.php?route=orders');
        }
        $items = Order::getOrderItems($orderId);
        $menuItems = MenuItem::all();
        include APP_ROOT . '/apps/views/waiter/order-view.php';
    }

    public function addItem(): void {
        requireRole(['waiter']);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $menuItemId = (int)($_POST['menu_item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $instructions = trim($_POST['instructions'] ?? '');
        
        if ($orderId > 0 && $menuItemId > 0 && $quantity > 0) {
            OrderService::addItem($orderId, $menuItemId, $quantity, $instructions);
        }
        redirect('index.php?route=order-view&id=' . $orderId);
    }

    public function removeItem(): void {
        requireRole(['waiter']);
        $itemId = (int)($_GET['item_id'] ?? 0);
        $orderId = (int)($_GET['order_id'] ?? 0);
        if ($itemId > 0) {
            OrderService::removeItem($itemId);
        }
        redirect('index.php?route=order-view&id=' . $orderId);
    }

    public function printReceipt(): void {
        requireRole(['waiter']);
        $orderId = (int)($_GET['order_id'] ?? 0);
        $type = trim($_GET['type'] ?? 'customer');
        $order = Order::findById($orderId);
        
        if (!$order) {
            redirect('index.php?route=orders');
        }
        
        $receipt = Receipt::create($orderId, $type);
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="receipt-' . $receipt['receipt_code'] . '.txt"');
        echo $receipt['content'];
        exit;
    }

    public function complete(): void {
        requireRole(['cashier', 'admin', 'supervisor']);
        $orderId = (int)($_GET['id'] ?? 0);
        if ($orderId > 0) {
            OrderService::complete($orderId);
        }
        redirect('index.php?route=orders');
    }
}

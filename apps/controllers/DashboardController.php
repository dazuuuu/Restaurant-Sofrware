<?php
class DashboardController {
    public function index(): void {
        requireLogin();
        $user = currentUser();
        $menuItems = MenuService::all();
        $services = ServiceService::all();
        $bookings = BookingService::all();
        $users = User::all();
        $orders = Order::all();

        if (($user['role'] ?? '') === 'admin' || ($user['role'] ?? '') === 'supervisor') {
            include APP_ROOT . '/apps/views/admin/dashboard.php';
            return;
        }
        if (($user['role'] ?? '') === 'cashier') {
            $pendingOrders = array_filter($orders, fn($o) => $o['status'] === 'pending');
            include APP_ROOT . '/apps/views/cashier/dashboard.php';
            return;
        }
        if (($user['role'] ?? '') === 'waiter') {
            $activeOrders = array_filter($orders, fn($o) => in_array($o['status'], ['pending', 'ready']));
            include APP_ROOT . '/apps/views/waiter/dashboard.php';
            return;
        }
        if (($user['role'] ?? '') === 'reception') {
            include APP_ROOT . '/apps/views/reception/dashboard.php';
            return;
        }

        include APP_ROOT . '/apps/views/layouts/guest.php';
    }
}

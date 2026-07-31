<?php
class BookingController {
    public function index(): void {
        requireLogin();
        $bookings = BookingService::all();
        include APP_ROOT . '/apps/views/admin/bookings.php';
    }

    public function create(): void {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            BookingService::create([
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'contact' => trim($_POST['contact'] ?? ''),
                'event_type' => trim($_POST['event_type'] ?? 'Event'),
                'table_name' => trim($_POST['table_name'] ?? ''),
                'deposit' => trim($_POST['deposit'] ?? 0),
                'food_items' => trim($_POST['food_items'] ?? ''),
                'service_items' => trim($_POST['service_items'] ?? ''),
                'attendees' => trim($_POST['attendees'] ?? 1),
                'status' => trim($_POST['status'] ?? 'pending'),
                'created_by' => currentUser()['id'] ?? null
            ]);
            redirect('/POS/public/index.php?route=bookings');
        }
        include APP_ROOT . '/apps/views/admin/booking-form.php';
    }

    public function edit(): void {
        requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $booking = Booking::findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            BookingService::update($id, [
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'contact' => trim($_POST['contact'] ?? ''),
                'event_type' => trim($_POST['event_type'] ?? 'Event'),
                'table_name' => trim($_POST['table_name'] ?? ''),
                'deposit' => trim($_POST['deposit'] ?? 0),
                'food_items' => trim($_POST['food_items'] ?? ''),
                'service_items' => trim($_POST['service_items'] ?? ''),
                'attendees' => trim($_POST['attendees'] ?? 1),
                'status' => trim($_POST['status'] ?? 'pending')
            ]);
            redirect('/POS/public/index.php?route=bookings');
        }
        include APP_ROOT . '/apps/views/admin/booking-form.php';
    }
}

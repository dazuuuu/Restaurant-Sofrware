<?php
class BookingApiController {
    public function create(): void {
        $user = requireApiRole(['reception', 'admin', 'supervisor']);

        $customerName = trim($_POST['customer_name'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        if ($customerName === '' || $contact === '') {
            apiError('customer_name and contact are required.');
        }

        $booking = BookingService::create([
            'client_id' => trim($_POST['client_id'] ?? '') ?: null,
            'customer_name' => $customerName,
            'contact' => $contact,
            'event_type' => trim($_POST['event_type'] ?? 'Event'),
            'table_name' => trim($_POST['table_name'] ?? ''),
            'deposit' => trim($_POST['deposit'] ?? 0),
            'food_items' => trim($_POST['food_items'] ?? ''),
            'service_items' => trim($_POST['service_items'] ?? ''),
            'attendees' => trim($_POST['attendees'] ?? 1),
            'status' => trim($_POST['status'] ?? 'pending'),
            'created_by' => $user['id'],
        ]);

        apiRespond(['booking' => $booking]);
    }
}

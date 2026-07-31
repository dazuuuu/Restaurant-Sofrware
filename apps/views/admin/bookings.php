<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bookings</h2>
        <a class="btn btn-primary" href="index.php?route=bookings-create">New Booking</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Code</th>
                <th>Customer</th>
                <th>Event</th>
                <th>Table</th>
                <th>Deposit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= e($booking['booking_code']) ?></td>
                    <td><?= e($booking['customer_name']) ?></td>
                    <td><?= e($booking['event_type']) ?></td>
                    <td><?= e($booking['table_name']) ?></td>
                    <td><?= formatCurrency($booking['deposit']) ?></td>
                    <td><?= e($booking['status']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?route=bookings-edit&id=<?= (int)$booking['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

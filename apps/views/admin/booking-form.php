<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <h2><?= isset($booking) && $booking ? 'Edit Booking' : 'Create Booking' ?></h2>
    <form method="post" class="mt-3" id="booking-form">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Customer Name</label>
                <input class="form-control" name="customer_name" value="<?= isset($booking['customer_name']) ? e($booking['customer_name']) : '' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact</label>
                <input class="form-control" name="contact" value="<?= isset($booking['contact']) ? e($booking['contact']) : '' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Event Type</label>
                <input class="form-control" name="event_type" value="<?= isset($booking['event_type']) ? e($booking['event_type']) : 'Event' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Table</label>
                <input class="form-control" name="table_name" value="<?= isset($booking['table_name']) ? e($booking['table_name']) : '' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Deposit</label>
                <input class="form-control" name="deposit" type="number" step="0.01" value="<?= isset($booking['deposit']) ? e($booking['deposit']) : '0' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Attendees</label>
                <input class="form-control" name="attendees" type="number" value="<?= isset($booking['attendees']) ? e($booking['attendees']) : '1' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="pending" <?= (isset($booking['status']) && $booking['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?= (isset($booking['status']) && $booking['status'] === 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?= (isset($booking['status']) && $booking['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Food Items</label>
                <textarea class="form-control" name="food_items" rows="3"><?= isset($booking['food_items']) ? e($booking['food_items']) : '' ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Service Items</label>
                <textarea class="form-control" name="service_items" rows="3"><?= isset($booking['service_items']) ? e($booking['service_items']) : '' ?></textarea>
            </div>
        </div>
        <button class="btn btn-primary mt-3">Save Booking</button>
    </form>
</div>
<?php if (empty($booking)): ?>
<script>
    document.getElementById('booking-form').addEventListener('submit', async function (e) {
        if (typeof posDb === 'undefined' || navigator.onLine) {
            return; // online, or offline scripts unavailable: let normal server POST happen unchanged
        }
        e.preventDefault();

        const form = e.target;
        const clientId = uuid();
        const payload = {
            client_id: clientId,
            customer_name: form.customer_name.value,
            contact: form.contact.value,
            event_type: form.event_type.value,
            table_name: form.table_name.value,
            deposit: form.deposit.value,
            attendees: form.attendees.value,
            status: form.status.value,
            food_items: form.food_items.value,
            service_items: form.service_items.value,
        };
        await posDb.bookings.put({ ...payload, local_key: clientId });
        await enqueueSync('booking-create', payload);

        form.classList.add('d-none');
        const notice = document.createElement('div');
        notice.className = 'alert alert-warning mt-3';
        notice.textContent = "You're offline. This booking is saved on this device and will sync automatically once you're back online.";
        form.insertAdjacentElement('afterend', notice);
    });
</script>
<?php endif; ?>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

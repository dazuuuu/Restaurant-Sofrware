<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <h2>Create New Order</h2>
    <form method="post" class="mt-4" id="order-create-form">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Table Number</label>
                <input type="text" class="form-control form-control-lg" name="table_number" placeholder="e.g., Table 1, T01" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Customer Name (Optional)</label>
                <input type="text" class="form-control form-control-lg" name="customer_name" placeholder="Leave blank for walk-in">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Start Order</button>
            <a href="index.php?route=orders" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
    <div id="order-workspace" class="mt-4"></div>
</div>
<script>
    document.getElementById('order-create-form').addEventListener('submit', async function (e) {
        if (typeof posDb === 'undefined' || navigator.onLine) {
            return; // online: let the normal server POST/redirect happen unchanged
        }
        e.preventDefault();

        const form = e.target;
        const clientId = uuid();
        await posDb.orders.put({
            local_key: clientId,
            client_id: clientId,
            table_number: form.table_number.value,
            customer_name: form.customer_name.value,
            status: 'pending',
            total_amount: 0,
        });
        await enqueueSync('order-create', {
            client_id: clientId,
            table_number: form.table_number.value,
            customer_name: form.customer_name.value,
        });

        form.classList.add('d-none');
        await waiterOrder.renderOrderWorkspace('#order-workspace', clientId);
    });
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

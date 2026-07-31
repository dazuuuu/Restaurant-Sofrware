<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container-fluid">
    <h2 class="mb-4">Cashier Dashboard</h2>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Confirm payments and manage orders ready for payment
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Orders Ready for Payment</h5>
                </div>
                <div class="card-body">
                    <?php $currency = Settings::get('currency_symbol', 'Ksh'); ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pendingOrders)): ?>
                                    <?php foreach ($pendingOrders as $order): ?>
                                        <tr id="order-row-<?= $order['id'] ?>">
                                            <td><strong><?= e($order['order_code']) ?></strong></td>
                                            <td><?= e($order['table_number']) ?></td>
                                            <td><?= e($order['customer_name'] ?: 'Walk-in') ?></td>
                                            <td class="text-primary"><strong><?= $currency . number_format($order['total_amount'], 2) ?></strong></td>
                                            <td><span class="badge bg-warning">Ready</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="setOrderId(<?= $order['id'] ?>, '<?= e($order['order_code']) ?>', <?= $order['total_amount'] ?>, '<?= e($order['client_id'] ?? '') ?>')">Confirm Payment</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No pending orders</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6>Daily Collections</h6>
                    <div class="h3 mt-3"><?= $currency . number_format(array_sum(array_column($pendingOrders, 'total_amount')), 2) ?></div>
                    <small>Pending Payments</small>
                </div>
            </div>
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6>Quick Info</h6>
                    <p class="mb-2"><small><strong>Total Orders:</strong> <?= count($pendingOrders) ?></small></p>
                    <p class="mb-0"><small><strong>Avg Order:</strong> <?= count($pendingOrders) > 0 ? $currency . number_format(array_sum(array_column($pendingOrders, 'total_amount')) / count($pendingOrders), 2) : $currency . '0.00' ?></small></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Order:</strong> <span id="orderCode"></span></p>
                <p><strong>Amount:</strong> <span id="orderAmount"></span></p>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" id="paymentMethod">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processPayment()">Complete Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
    (async function () {
        if (typeof posDb === 'undefined') {
            return;
        }
        await kitchenOffline.cacheKitchenOrders(<?= json_encode($pendingOrders) ?>);
    })();

    let currentOrderId = null;
    let currentClientId = null;
    let currentAmount = null;

    function setOrderId(id, code, amount, clientId) {
        currentOrderId = id;
        currentClientId = clientId || null;
        document.getElementById('orderCode').textContent = code;
        document.getElementById('orderAmount').textContent = '<?= Settings::get("currency_symbol", "Ksh") ?>' + parseFloat(amount).toFixed(2);
        document.getElementById('paymentMethod').value = 'cash';
    }

    async function processPayment() {
        if (!currentOrderId) {
            return;
        }

        if (typeof posDb !== 'undefined' && !navigator.onLine) {
            const localKey = currentClientId || 'srv_' + currentOrderId;
            await posDb.orders.update(localKey, { status: 'completed' });
            await enqueueSync('order-complete', {
                order_id: currentOrderId || undefined,
                order_client_id: currentClientId || undefined,
            });
            alert('Payment recorded locally. This will sync once you\'re back online.');
            document.getElementById('order-row-' + currentOrderId)?.remove();
            bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
            return;
        }

        alert('Payment processed successfully!');
        location.href = 'index.php?route=order-complete&id=' + currentOrderId;
    }
</script>

<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

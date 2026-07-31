<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Orders</h2>
        <a href="index.php?route=order-create" class="btn btn-primary">New Order</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Active Orders</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                <?php $currency = Settings::get('currency_symbol', 'Ksh'); ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php if (in_array($order['status'], ['pending', 'ready', 'partial'])): ?>
                                        <tr>
                                            <td><strong><?= e($order['order_code']) ?></strong></td>
                                            <td><?= e($order['table_number']) ?></td>
                                            <td><?= e($order['customer_name'] ?: 'Walk-in') ?></td>
                                            <td><?= $currency . number_format($order['total_amount'], 2) ?></td>
                                            <td>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php elseif ($order['status'] === 'ready'): ?>
                                                    <span class="badge bg-success">Ready</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Partial</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="index.php?route=order-view&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5>Quick Stats</h5>
                    <div class="mb-3">
                        <small class="text-muted">Active Orders</small>
                        <div class="h4"><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['pending', 'ready', 'partial']))) ?></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Total Today</small>
                        <div class="h4"><?= $currency . number_format(array_sum(array_column($orders, 'total_amount')), 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="offlineOrderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="offline-order-modal-body"></div>
        </div>
    </div>
</div>

<script>
    (async function () {
        if (typeof posDb === 'undefined') {
            return;
        }
        const serverOrders = <?= json_encode($orders) ?>;
        for (const o of serverOrders) {
            const localKey = o.client_id || 'srv_' + o.id;
            const existing = await posDb.orders.get(localKey);
            if (!existing) {
                await posDb.orders.put({ ...o, local_key: localKey });
            }
        }

        const knownCodes = new Set(serverOrders.map((o) => o.order_code));
        const localOrders = await posDb.orders.toArray();
        const currencySymbol = (await getSession())?.currencySymbol || 'Ksh';
        const tbody = document.getElementById('orders-table-body');

        localOrders
            .filter((o) => ['pending', 'ready', 'partial'].includes(o.status) && !knownCodes.has(o.order_code))
            .forEach((o) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${o.order_code || 'Pending sync'}</strong></td>
                    <td>${o.table_number}</td>
                    <td>${o.customer_name || 'Walk-in'}</td>
                    <td>${currencySymbol}${Number(o.total_amount || 0).toFixed(2)}</td>
                    <td><span class="badge bg-secondary">Not yet synced</span></td>
                    <td><button type="button" class="btn btn-sm btn-outline-primary view-offline-order" data-local-key="${o.local_key}">View</button></td>
                `;
                tbody.appendChild(tr);
            });

        document.querySelectorAll('.view-offline-order').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const modal = new bootstrap.Modal(document.getElementById('offlineOrderModal'));
                await waiterOrder.renderOrderWorkspace('#offline-order-modal-body', btn.getAttribute('data-local-key'));
                modal.show();
            });
        });
    })();
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Kitchen Dashboard</h2>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>Pending Orders:</strong> Please prepare these orders
            </div>
        </div>
    </div>

    <div class="row">
        <?php $currency = Settings::get('currency_symbol', 'Ksh'); ?>
        <?php foreach ($orders as $order): ?>
            <?php if (in_array($order['status'], ['pending', 'partial'])): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm border-<?= $order['status'] === 'pending' ? 'danger' : 'warning' ?>">
                        <div class="card-header bg-<?= $order['status'] === 'pending' ? 'danger' : 'warning' ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0"><?= e($order['order_code']) ?></h5>
                                    <small>Table <?= e($order['table_number']) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark">Items: <?= count(Order::getOrderItems($order['id'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>Items to Prepare:</h6>
                            <ul class="list-unstyled">
                                <?php foreach (Order::getOrderItems($order['id']) as $item): ?>
                                    <li class="mb-2">
                                        <strong><?= $item['quantity'] ?>x</strong> <?= e($item['name']) ?>
                                        <?php if ($item['special_instructions']): ?>
                                            <br><small class="text-muted">📝 <?= e($item['special_instructions']) ?></small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($order['notes']): ?>
                                <div class="alert alert-warning mt-2">
                                    <small><strong>Notes:</strong> <?= e($order['notes']) ?></small>
                                </div>
                            <?php endif; ?>
                            <div class="mt-3 d-grid gap-2">
                                <a href="index.php?route=kitchen-view&id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                <a href="index.php?route=kitchen-ready&id=<?= $order['id'] ?>"
                                   class="btn btn-success btn-sm mark-ready-link"
                                   data-order-id="<?= $order['id'] ?>"
                                   data-client-id="<?= e($order['client_id'] ?? '') ?>">Mark as Ready</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty(array_filter($orders, fn($o) => in_array($o['status'], ['pending', 'partial'])))): ?>
            <div class="col-md-12">
                <div class="alert alert-success text-center">
                    <h5>✓ All orders are prepared!</h5>
                    <p class="mb-0">No pending orders at the moment.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h5>Completed Orders</h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Items</th>
                            <th>Time Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php if ($order['status'] === 'ready'): ?>
                                <tr>
                                    <td><?= e($order['order_code']) ?></td>
                                    <td><?= e($order['table_number']) ?></td>
                                    <td><?= count(Order::getOrderItems($order['id'])) ?></td>
                                    <td><?= $order['completed_at'] ? date('H:i:s', strtotime($order['completed_at'])) : '-' ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    (async function () {
        if (typeof posDb === 'undefined') {
            return;
        }
        await kitchenOffline.cacheKitchenOrders(<?= json_encode($orders) ?>);
        kitchenOffline.wireMarkReadyLinks('.mark-ready-link');
    })();
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

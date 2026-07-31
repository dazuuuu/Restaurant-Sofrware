<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Order <?= e($order['order_code']) ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Table:</strong> <?= e($order['table_number']) ?></p>
                            <p><strong>Customer:</strong> <?= e($order['customer_name'] ?: 'Walk-in') ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Time Received:</strong> <?= date('H:i:s', strtotime($order['created_at'])) ?></p>
                            <p><strong>Status:</strong> <span class="badge bg-warning">Preparing</span></p>
                        </div>
                    </div>

                    <hr>

                    <h5>Items to Prepare</h5>
                    <div class="list-group">
                        <?php foreach ($items as $item): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><strong><?= $item['quantity'] ?>x</strong> <?= e($item['name']) ?></h6>
                                        <p class="mb-1 text-muted"><?= e($item['category']) ?></p>
                                        <?php if ($item['special_instructions']): ?>
                                            <p class="mb-0"><small class="bg-warning-subtle p-2 rounded">📝 <?= e($item['special_instructions']) ?></small></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($order['notes']): ?>
                        <div class="alert alert-warning mt-3">
                            <strong>Special Instructions:</strong> <?= e($order['notes']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 d-grid gap-2">
                        <a href="index.php?route=kitchen-ready&id=<?= $order['id'] ?>"
                           class="btn btn-success btn-lg mark-ready-link"
                           data-order-id="<?= $order['id'] ?>"
                           data-client-id="<?= e($order['client_id'] ?? '') ?>">✓ Mark as Ready for Pickup</a>
                        <a href="index.php?route=kitchen-dashboard" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5>Preparation Tips</h5>
                    <ul class="mb-0">
                        <li>Follow the special instructions carefully</li>
                        <li>Check item quantity before serving</li>
                        <li>Ensure quality and presentation</li>
                        <li>Mark as ready when complete</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (async function () {
        if (typeof posDb === 'undefined') {
            return;
        }
        await kitchenOffline.cacheKitchenOrders([<?= json_encode($order) ?>]);
        kitchenOffline.wireMarkReadyLinks('.mark-ready-link');
    })();
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

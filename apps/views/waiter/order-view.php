<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container-fluid py-4">
    <div id="order-workspace" class="d-none"></div>
    <div class="row" id="order-view-static">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?= e($order['order_code']) ?> - Table <?= e($order['table_number']) ?></h5>
                            <small class="text-muted"><?= e($order['customer_name'] ?: 'Walk-in') ?></small>
                        </div>
                        <div>
                            <?php if ($order['status'] === 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php elseif ($order['status'] === 'ready'): ?>
                                <span class="badge bg-success">Ready</span>
                            <?php else: ?>
                                <span class="badge bg-info"><?= ucfirst($order['status']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Order Items</h6>
                    <?php if ($items): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $currency = Settings::get('currency_symbol', 'Ksh'); ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?= e($item['name']) ?></td>
                                            <td><?= e($item['category']) ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= $currency . number_format($item['unit_price'], 2) ?></td>
                                            <td><?= $currency . number_format($item['subtotal'], 2) ?></td>
                                            <td><small><?= e($item['special_instructions'] ?: '-') ?></small></td>
                                            <td>
                                                <a href="index.php?route=order-remove-item&item_id=<?= $item['id'] ?>&order_id=<?= $order['id'] ?>" class="btn btn-sm btn-danger">Remove</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No items added yet. Add items below.</p>
                    <?php endif; ?>

                    <hr>

                    <h6>Add Item to Order</h6>
                    <form method="post" action="index.php?route=order-add-item">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <div class="row g-2 mb-3">
                            <div class="col-md-5">
                                <select class="form-select" name="menu_item_id" required>
                                    <option value="">-- Select Item --</option>
                                    <?php foreach ($menuItems as $menuItem): ?>
                                        <option value="<?= $menuItem['id'] ?>"><?= e($menuItem['name']) ?> - <?= $currency . number_format($menuItem['price'], 2) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="quantity" value="1" min="1" max="99" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="instructions" placeholder="Special instructions (optional)">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Items</small>
                        <div class="h5"><?= count($items) ?></div>
                    </div>
                    <div class="mb-3 border-top pt-3">
                        <small class="text-muted">Order Total</small>
                        <div class="h4 text-primary"><?= $currency . number_format($order['total_amount'], 2) ?></div>
                    </div>

                    <div class="btn-group d-grid gap-2">
                        <a href="index.php?route=print-receipt&order_id=<?= $order['id'] ?>&type=customer" class="btn btn-outline-primary" target="_blank">
                            🖨 Print Customer Receipt
                        </a>
                        <a href="index.php?route=print-receipt&order_id=<?= $order['id'] ?>&type=kitchen" class="btn btn-outline-danger" target="_blank">
                            🖨 Print Kitchen Ticket
                        </a>
                    </div>

                    <div class="mt-3 btn-group d-grid gap-2">
                        <a href="index.php?route=orders" class="btn btn-secondary">Back to Orders</a>
                    </div>
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
        const serverOrder = <?= json_encode($order) ?>;
        const serverItems = <?= json_encode($items) ?>;
        const localKey = await waiterOrder.upsertOrderFromServer(serverOrder, serverItems);

        if (!navigator.onLine) {
            document.getElementById('order-view-static').classList.add('d-none');
            document.getElementById('order-workspace').classList.remove('d-none');
            await waiterOrder.renderOrderWorkspace('#order-workspace', localKey);
        }
    })();
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

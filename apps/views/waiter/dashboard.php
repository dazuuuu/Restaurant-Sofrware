<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Management</h2>
        <a href="index.php?route=order-create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Order</a>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Active Orders</h5>
                </div>
                <div class="card-body">
                    <?php $currency = Settings::get('currency_symbol', 'Ksh'); ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Table</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activeOrders)): ?>
                                    <?php foreach ($activeOrders as $order): ?>
                                        <tr>
                                            <td><strong><?= e($order['order_code']) ?></strong></td>
                                            <td><?= e($order['table_number']) ?></td>
                                            <td><?= count(Order::getOrderItems($order['id'])) ?> items</td>
                                            <td class="text-primary"><strong><?= $currency . number_format($order['total_amount'], 2) ?></strong></td>
                                            <td>
                                                <?php if ($order['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Ready</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="index.php?route=order-view&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No active orders</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6>Active Orders</h6>
                    <div class="h3 mt-3"><?= count($activeOrders) ?></div>
                </div>
            </div>
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6>Quick Actions</h6>
                    <a href="index.php?route=order-create" class="btn btn-sm btn-primary w-100 mb-2">
                        <i class="bi bi-plus"></i> New Order
                    </a>
                    <a href="index.php?route=orders" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-list"></i> View All
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

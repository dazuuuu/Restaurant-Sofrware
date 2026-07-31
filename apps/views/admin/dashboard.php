<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container-fluid py-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Users</h6>
                    <p class="text-muted">Manage staff accounts</p>
                    <a class="btn btn-primary" href="index.php?route=users">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Menu</h6>
                    <p class="text-muted">Update food items</p>
                    <a class="btn btn-primary" href="index.php?route=menu">Manage Menu</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Services</h6>
                    <p class="text-muted">Manage spa, massage, accommodation</p>
                    <a class="btn btn-primary" href="index.php?route=services">Manage Services</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Settings</h6>
                    <p class="text-muted">Configure system</p>
                    <a class="btn btn-primary" href="index.php?route=settings">View Settings</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Bookings</h6>
                    <p class="text-muted">Event reservations</p>
                    <a class="btn btn-primary" href="index.php?route=bookings">View Bookings</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Orders</h6>
                    <p class="text-muted">Customer orders</p>
                    <a class="btn btn-primary" href="index.php?route=orders">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Kitchen</h6>
                    <p class="text-muted">Order preparation</p>
                    <a class="btn btn-primary" href="index.php?route=kitchen-dashboard">Kitchen Dashboard</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6>Quick Stats</h6>
                    <small class="text-muted">Total Earnings Today</small><br>
                    <strong class="text-primary"><?= Settings::get('currency_symbol', 'Ksh') . number_format(array_sum(array_column($orders, 'total_amount')), 2) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Active Menu Items</h5>
                    <ul>
                        <?php foreach (array_slice($menuItems, 0, 5) as $item): ?>
                            <li><?= e($item['name']) ?> - <?= Settings::get('currency_symbol', 'Ksh') . number_format($item['price'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Recent Bookings</h5>
                    <ul>
                        <?php foreach (array_slice($bookings, 0, 5) as $booking): ?>
                            <li><?= e($booking['customer_name']) ?> - <?= e($booking['event_type']) ?> - <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : 'warning' ?>"><?= e($booking['status']) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

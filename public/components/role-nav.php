<?php if (isLoggedIn()): ?>
    <div class="list-group mb-4">
        <a class="list-group-item list-group-item-action" href="index.php?route=dashboard">Dashboard</a>
        <?php if (in_array(currentRole(), ['admin', 'supervisor'], true)): ?>
            <a class="list-group-item list-group-item-action" href="index.php?route=users">Users</a>
            <a class="list-group-item list-group-item-action" href="index.php?route=menu">Menu</a>
            <a class="list-group-item list-group-item-action" href="index.php?route=services">Services</a>
            <a class="list-group-item list-group-item-action" href="index.php?route=bookings">Bookings</a>
        <?php endif; ?>
        <?php if (currentRole() === 'cashier'): ?>
            <a class="list-group-item list-group-item-action" href="index.php?route=bookings">Payments & Bookings</a>
        <?php endif; ?>
        <?php if (currentRole() === 'waiter'): ?>
            <a class="list-group-item list-group-item-action" href="index.php?route=menu">Orders & Menu</a>
        <?php endif; ?>
        <?php if (currentRole() === 'reception'): ?>
            <a class="list-group-item list-group-item-action" href="index.php?route=bookings">Bookings & Guest Services</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

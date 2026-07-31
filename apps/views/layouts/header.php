<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant POS</title>
    <link rel="manifest" href="manifest.json">
    <link rel="icon" href="assets/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/icons/icon.svg">
    <meta name="theme-color" content="#667eea">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }

        .sidebar-header h5 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin-bottom: 5px;
        }

        .sidebar-nav a {
            display: block;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .sidebar-nav a:hover {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .sidebar-nav a.active {
            background-color: rgba(255,255,255,0.3);
            color: white;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: #667eea !important;
            font-size: 18px;
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a428f 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-nav {
                display: flex;
                flex-wrap: wrap;
            }
            .sidebar-nav li {
                flex: 1;
                min-width: 150px;
            }
        }
    </style>
</head>
<body>
<?php if (isLoggedIn()): ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h5>
                <i class="bi bi-shop"></i>
                POS System
            </h5>
            <small><?= e(roleLabel(currentRole())) ?></small>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php?route=dashboard" class="<?= ($_GET['route'] ?? '') === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-house-door"></i> Dashboard</a></li>
            
            <?php if (in_array(currentRole(), ['admin', 'supervisor'], true)): ?>
                <li class="mt-3 mb-2"><small class="text-uppercase">Management</small></li>
                <li><a href="index.php?route=users" class="<?= ($_GET['route'] ?? '') === 'users' ? 'active' : '' ?>"><i class="bi bi-people"></i> Users</a></li>
                <li><a href="index.php?route=menu" class="<?= ($_GET['route'] ?? '') === 'menu' ? 'active' : '' ?>"><i class="bi bi-cup-straw"></i> Menu</a></li>
                <li><a href="index.php?route=services" class="<?= ($_GET['route'] ?? '') === 'services' ? 'active' : '' ?>"><i class="bi bi-heart"></i> Services</a></li>
                <li><a href="index.php?route=bookings" class="<?= ($_GET['route'] ?? '') === 'bookings' ? 'active' : '' ?>"><i class="bi bi-calendar-event"></i> Bookings</a></li>
                <li><a href="index.php?route=orders" class="<?= ($_GET['route'] ?? '') === 'orders' ? 'active' : '' ?>"><i class="bi bi-receipt"></i> Orders</a></li>
                <li><a href="index.php?route=kitchen-dashboard" class="<?= ($_GET['route'] ?? '') === 'kitchen-dashboard' ? 'active' : '' ?>"><i class="bi bi-fire"></i> Kitchen</a></li>
                <li><a href="index.php?route=settings" class="<?= ($_GET['route'] ?? '') === 'settings' ? 'active' : '' ?>"><i class="bi bi-gear"></i> Settings</a></li>
            <?php endif; ?>

            <?php if (currentRole() === 'cashier'): ?>
                <li><a href="index.php?route=orders" class="<?= ($_GET['route'] ?? '') === 'orders' ? 'active' : '' ?>"><i class="bi bi-receipt"></i> Payments</a></li>
                <li><a href="index.php?route=bookings" class="<?= ($_GET['route'] ?? '') === 'bookings' ? 'active' : '' ?>"><i class="bi bi-calendar-event"></i> Bookings</a></li>
            <?php endif; ?>

            <?php if (currentRole() === 'waiter'): ?>
                <li><a href="index.php?route=orders" class="<?= ($_GET['route'] ?? '') === 'orders' ? 'active' : '' ?>"><i class="bi bi-receipt"></i> Orders</a></li>
                <li><a href="index.php?route=order-create" class="<?= ($_GET['route'] ?? '') === 'order-create' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> New Order</a></li>
            <?php endif; ?>

            <?php if (currentRole() === 'reception'): ?>
                <li><a href="index.php?route=bookings" class="<?= ($_GET['route'] ?? '') === 'bookings' ? 'active' : '' ?>"><i class="bi bi-calendar-event"></i> Bookings</a></li>
                <li><a href="index.php?route=bookings-create" class="<?= ($_GET['route'] ?? '') === 'bookings-create' ? 'active' : '' ?>"><i class="bi bi-plus-circle"></i> New Booking</a></li>
            <?php endif; ?>

            <li class="mt-4 pt-4 border-top"><a href="index.php?route=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <?php if (isFloorOpsRole(currentRole())): ?>
                    <span id="sync-status" class="badge bg-warning text-dark d-none"></span>
                <?php endif; ?>
                <span class="navbar-text ms-auto">
                    <small class="text-muted">Logged in as: <strong><?= e(currentUser()['full_name']) ?></strong></small>
                </span>
            </div>
        </nav>
        <div class="content">
<?php else: ?>
    <!--- Login page without sidebar --->
<?php endif; ?>

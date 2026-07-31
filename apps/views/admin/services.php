<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Services Management</h2>
        <a class="btn btn-primary" href="index.php?route=services-create">Add Service</a>
    </div>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5><?= e($item['name']) ?></h5>
                        <p class="text-muted mb-2"><?= e($item['category']) ?></p>
                        <p><?= e($item['description']) ?></p>
                        <p class="fw-bold"><?= formatCurrency($item['price']) ?></p>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="index.php?route=services-edit&id=<?= (int)$item['id'] ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="index.php?route=services-delete&id=<?= (int)$item['id'] ?>">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

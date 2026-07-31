<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <h2><?= isset($item) && $item ? 'Edit Menu Item' : 'Create Menu Item' ?></h2>
    <form method="post" class="mt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" value="<?= isset($item['name']) ? e($item['name']) : '' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <input class="form-control" name="category" value="<?= isset($item['category']) ? e($item['category']) : 'Food' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Price</label>
                <input class="form-control" name="price" type="number" step="0.01" value="<?= isset($item['price']) ? e($item['price']) : '' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Image URL</label>
                <input class="form-control" name="image" value="<?= isset($item['image']) ? e($item['image']) : '' ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"><?= isset($item['description']) ? e($item['description']) : '' ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (isset($item['status']) && $item['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (isset($item['status']) && $item['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary mt-3">Save Item</button>
    </form>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

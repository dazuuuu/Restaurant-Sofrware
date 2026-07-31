<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <h2><?= isset($item) && $item ? 'Edit User' : 'Create User' ?></h2>
    <form method="post" class="mt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input class="form-control" name="full_name" value="<?= isset($item['full_name']) ? e($item['full_name']) : '' ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" value="<?= isset($item['email']) ? e($item['email']) : '' ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" id="roleSelect">
                    <option value="cashier" <?= (isset($item['role']) && $item['role'] === 'cashier') ? 'selected' : '' ?>>Cashier</option>
                    <option value="waiter" <?= (isset($item['role']) && $item['role'] === 'waiter') ? 'selected' : '' ?>>Waiter</option>
                    <option value="reception" <?= (isset($item['role']) && $item['role'] === 'reception') ? 'selected' : '' ?>>Reception</option>
                    <option value="supervisor" <?= (isset($item['role']) && $item['role'] === 'supervisor') ? 'selected' : '' ?>>Supervisor</option>
                    <option value="admin" <?= (isset($item['role']) && $item['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="active" <?= (isset($item['status']) && $item['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (isset($item['status']) && $item['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <!-- Password field for Admin/Supervisor -->
            <div class="col-md-6" id="passwordDiv" style="display: none;">
                <label class="form-label">Password</label>
                <input class="form-control" name="password" type="password">
                <small class="text-muted">Leave blank to keep existing password</small>
            </div>

            <!-- PIN field for Staff -->
            <div class="col-md-6" id="pinDiv" style="display: none;">
                <label class="form-label">PIN (4 digits)</label>
                <input class="form-control" name="pin" type="text" maxlength="4" placeholder="e.g., 1234" inputmode="numeric">
                <small class="text-muted">Leave blank to keep existing PIN</small>
            </div>
        </div>
        <button class="btn btn-primary mt-3">Save User</button>
    </form>

    <script>
        function updateAuthField() {
            const role = document.getElementById('roleSelect').value;
            const passwordDiv = document.getElementById('passwordDiv');
            const pinDiv = document.getElementById('pinDiv');
            const passwordInput = passwordDiv.querySelector('input');
            const pinInput = pinDiv.querySelector('input');

            if (role === 'admin' || role === 'supervisor') {
                passwordDiv.style.display = 'block';
                pinDiv.style.display = 'none';
                passwordInput.required = true;
                pinInput.required = false;
            } else {
                passwordDiv.style.display = 'none';
                pinDiv.style.display = 'block';
                passwordInput.required = false;
                pinInput.required = true;
            }
        }

        document.getElementById('roleSelect').addEventListener('change', updateAuthField);
        updateAuthField(); // Initial call
    </script>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

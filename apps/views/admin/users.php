<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Accounts</h2>
        <a class="btn btn-primary" href="index.php?route=users-create">Add User</a>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['full_name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e(roleLabel($user['role'])) ?></td>
                    <td><?= e($user['status']) ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?route=users-edit&id=<?= (int)$user['id'] ?>">Edit</a>
                        <a class="btn btn-sm btn-outline-danger" href="index.php?route=users-delete&id=<?= (int)$user['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

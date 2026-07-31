<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-3">Restaurant POS Login</h3>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= e($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="post" action="index.php?route=login">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="mt-3 text-muted small">
                        Demo admin: dazuhubs@gmail.com / password123
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

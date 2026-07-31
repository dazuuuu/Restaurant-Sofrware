<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-3">Admin Login</h3>
                    <p class="text-muted mb-3">Restaurant Management System</p>
                    <?php if (!empty($_SESSION['info'])): ?>
                        <div class="alert alert-success"><?= e($_SESSION['info']) ?></div>
                        <?php unset($_SESSION['info']); ?>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= e($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="post" action="index.php?route=login-admin">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="text-center mt-3 mb-0">
                        <a href="index.php?route=forgot-password">Forgot password?</a>
                    </p>
                    <hr>
                    <p class="text-center mb-0">
                        <a href="index.php?route=login-staff">Staff Login</a>
                    </p>
                    <div class="mt-3 text-muted small">
                        <strong>Demo Admin:</strong><br>
                        Email: dazuhubs@gmail.com<br>
                        Password: password123
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

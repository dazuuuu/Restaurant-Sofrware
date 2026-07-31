<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-3">Forgot Password</h3>
                    <p class="text-muted mb-3">Enter your admin email and we'll send you a 6-digit reset code.</p>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= e($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="post" action="index.php?route=forgot-password">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Code</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">
                        <a href="index.php?route=login-admin">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-3">Reset Password</h3>
                    <p class="text-muted mb-3">Enter the code sent to <strong><?= e($email) ?></strong> and choose a new password.</p>
                    <?php if (!empty($_SESSION['info'])): ?>
                        <div class="alert alert-info"><?= e($_SESSION['info']) ?></div>
                        <?php unset($_SESSION['info']); ?>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= e($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="post" action="index.php?route=reset-password">
                        <div class="mb-3">
                            <label class="form-label">6-Digit Code</label>
                            <input type="text" class="form-control" name="otp" inputmode="numeric" maxlength="6" pattern="\d{6}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" minlength="8" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">
                        <a href="index.php?route=forgot-password">Request a new code</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

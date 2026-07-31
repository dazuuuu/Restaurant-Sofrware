<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-4">
    <h2 class="mb-4">System Settings</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= e($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?route=settings-update">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Restaurant Name</label>
                                <input type="text" class="form-control" name="restaurant_name" value="<?= e($settings['restaurant_name'] ?? 'My Restaurant') ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" name="currency" value="<?= e($settings['currency'] ?? 'KSH') ?>" placeholder="e.g., KSH, USD, EUR">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Currency Symbol</label>
                                <input type="text" class="form-control" name="currency_symbol" value="<?= e($settings['currency_symbol'] ?? 'Ksh') ?>" placeholder="e.g., Ksh, $, €">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Logo URL</label>
                                <input type="url" class="form-control" name="logo_url" value="<?= e($settings['logo_url'] ?? '') ?>" placeholder="https://example.com/logo.png">
                                <small class="text-muted">Upload your logo and paste the URL here</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Receipt Footer Message</label>
                                <textarea class="form-control" name="receipt_footer" rows="3" placeholder="Thank you for your visit!"><?= e($settings['receipt_footer'] ?? '') ?></textarea>
                                <small class="text-muted">This message will appear at the bottom of printed receipts</small>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="card-body text-center">
                    <img src="<?= e($settings['logo_url'] ?? 'https://via.placeholder.com/200') ?>" alt="Logo" class="img-fluid mb-3" style="max-height: 150px;">
                    <h5><?= e($settings['restaurant_name'] ?? 'My Restaurant') ?></h5>
                    <p class="text-muted">Currency: <?= e($settings['currency'] ?? 'KSH') ?> (<?= e($settings['currency_symbol'] ?? 'Ksh') ?>)</p>
                    <div class="alert alert-info small">
                        <strong>Receipt Footer:</strong><br>
                        <?= e($settings['receipt_footer'] ?? 'Thank you for your visit!') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

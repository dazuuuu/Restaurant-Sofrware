<?php include APP_ROOT . '/apps/views/layouts/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-3">Staff Login</h3>
                    <p class="text-muted mb-3">Enter your credentials to access</p>
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= e($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <div id="staff-login-error" class="alert alert-danger d-none"></div>
                    <form method="post" action="index.php?route=login-staff" id="staff-login-form">
                        <div class="mb-3">
                            <label class="form-label">Select Your Name</label>
                            <select class="form-select" name="user_id" required>
                                <option value="">-- Choose Staff Member --</option>
                                <?php foreach ($staffUsers as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= e($user['full_name']) ?> (<?= e(roleLabel($user['role'])) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">PIN (4 digits)</label>
                            <input type="password" class="form-control form-control-lg" name="pin" inputmode="numeric" maxlength="4" placeholder="****" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">
                        <a href="index.php?route=login-admin">Admin Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('staff-login-form').addEventListener('submit', async function (e) {
        if (typeof offlineAuth === 'undefined') {
            return; // offline scripts unavailable; fall back to normal server POST
        }
        e.preventDefault();

        const form = e.target;
        const userId = Number(form.user_id.value);
        const pin = form.pin.value;
        const errorBox = document.getElementById('staff-login-error');
        errorBox.classList.add('d-none');

        let result;
        try {
            const body = new URLSearchParams({ action: 'device-login', user_id: userId, pin });
            const response = await fetch('api.php', { method: 'POST', body });
            const json = await response.json();
            if (response.ok && json.ok) {
                await offlineAuth.cacheAfterOnlineLogin(json.data.user, pin, json.data.device_token, json.data.currency_symbol);
                await cacheReferenceData(json.data);
                result = { success: true };
            } else {
                result = { success: false, reason: json.error || 'Invalid User ID or PIN.' };
            }
        } catch (networkErr) {
            result = await offlineAuth.tryOfflineUnlock(userId, pin);
        }

        if (result.success) {
            window.location.href = 'index.php?route=dashboard';
        } else {
            errorBox.textContent = result.reason;
            errorBox.classList.remove('d-none');
        }
    });
</script>
<?php include APP_ROOT . '/apps/views/layouts/footer.php'; ?>

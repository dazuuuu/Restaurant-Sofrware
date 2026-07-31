<?php if (isLoggedIn()): ?>
        </div>
    </div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
$loadOfflineAssets = isFloorOpsRole(currentRole()) || (!isLoggedIn() && ($_GET['route'] ?? '') === 'login-staff');
if ($loadOfflineAssets):
?>
<script src="https://cdn.jsdelivr.net/npm/dexie@4/dist/dexie.min.js"></script>
<script src="assets/js/db.js"></script>
<script src="assets/js/offline-auth.js"></script>
<script src="assets/js/sync.js"></script>
<script src="assets/js/waiter-order.js"></script>
<script src="assets/js/kitchen.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js').catch(function (err) {
            console.warn('Service worker registration failed:', err);
        });
    }
</script>
<?php endif; ?>
</body>
</html>

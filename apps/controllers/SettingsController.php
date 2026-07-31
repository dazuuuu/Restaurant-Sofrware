<?php
class SettingsController {
    public function index(): void {
        requireRole(['admin']);
        $settings = SettingsService::all();
        include APP_ROOT . '/apps/views/admin/settings.php';
    }

    public function update(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'restaurant_name' => trim($_POST['restaurant_name'] ?? 'My Restaurant'),
                'currency' => trim($_POST['currency'] ?? 'KSH'),
                'currency_symbol' => trim($_POST['currency_symbol'] ?? 'Ksh'),
                'logo_url' => trim($_POST['logo_url'] ?? ''),
                'receipt_footer' => trim($_POST['receipt_footer'] ?? '')
            ];
            
            SettingsService::update($data);
            $_SESSION['success'] = 'Settings updated successfully.';
        }
        redirect('index.php?route=settings');
    }
}

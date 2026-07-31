<?php
class AuthApiController {
    public function deviceLogin(): void {
        $userId = (int)($_POST['user_id'] ?? 0);
        $pin = trim($_POST['pin'] ?? '');

        $user = AuthService::staffLogin($userId, $pin);
        if (!$user) {
            apiError('Invalid User ID or PIN.', 401);
        }

        $deviceToken = DeviceToken::issue((int)$user['id']);

        $staffRoster = array_map(
            fn($u) => ['id' => $u['id'], 'full_name' => $u['full_name'], 'role' => $u['role']],
            array_filter(User::all(), fn($u) => !in_array($u['role'], ['admin', 'supervisor'], true) && $u['status'] === 'active')
        );

        apiRespond([
            'user' => sanitizeUser($user),
            'device_token' => $deviceToken,
            'staff_roster' => array_values($staffRoster),
            'menu_items' => MenuService::all(),
            'services' => ServiceService::all(),
            'currency_symbol' => Settings::getCurrencySymbol(),
        ]);
    }

    public function deviceCheck(): void {
        $user = resolveApiUser();
        if (!$user) {
            apiRespond(['valid' => false]);
            return;
        }
        apiRespond(['valid' => true, 'user' => sanitizeUser($user)]);
    }
}

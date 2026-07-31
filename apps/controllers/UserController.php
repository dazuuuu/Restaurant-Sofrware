<?php
class UserController {
    public function index(): void {
        requireRole(['admin', 'supervisor']);
        $users = User::all();
        include APP_ROOT . '/apps/views/admin/users.php';
    }

    public function create(): void {
        requireRole(['admin', 'supervisor']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'waiter'),
                'status' => trim($_POST['status'] ?? 'active')
            ];
            
            // For staff, use PIN; for admin/supervisor, use password
            if (in_array($data['role'], ['admin', 'supervisor'])) {
                $data['password'] = trim($_POST['password'] ?? '');
            } else {
                $data['pin'] = trim($_POST['pin'] ?? '');
            }
            
            UserService::create($data);
            redirect('index.php?route=users');
        }
        $item = null; // For distinguishing create vs edit in form
        include APP_ROOT . '/apps/views/admin/user-form.php';
    }

    public function edit(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        $user = User::findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'waiter'),
                'status' => trim($_POST['status'] ?? 'active')
            ];
            
            if (in_array($data['role'], ['admin', 'supervisor'])) {
                if (!empty(trim($_POST['password'] ?? ''))) {
                    $data['password'] = trim($_POST['password']);
                }
            } else {
                if (!empty(trim($_POST['pin'] ?? ''))) {
                    $data['pin'] = trim($_POST['pin']);
                }
            }
            
            UserService::update($id, $data);
            redirect('index.php?route=users');
        }
        $item = $user; // Reuse item for compatibility with form
        include APP_ROOT . '/apps/views/admin/user-form.php';
    }

    public function delete(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            UserService::delete($id);
        }
        redirect('/POS/public/index.php?route=users');
    }
}

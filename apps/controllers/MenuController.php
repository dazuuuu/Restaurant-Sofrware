<?php
class MenuController {
    public function index(): void {
        requireLogin();
        $items = MenuService::all();
        include APP_ROOT . '/apps/views/admin/menu.php';
    }

    public function create(): void {
        requireRole(['admin', 'supervisor']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            MenuService::create([
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Food'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => trim($_POST['price'] ?? 0),
                'image' => trim($_POST['image'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ]);
            redirect('/POS/public/index.php?route=menu');
        }
        include APP_ROOT . '/apps/views/admin/menu-form.php';
    }

    public function edit(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        $item = MenuItem::findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            MenuService::update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Food'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => trim($_POST['price'] ?? 0),
                'image' => trim($_POST['image'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ]);
            redirect('/POS/public/index.php?route=menu');
        }
        include APP_ROOT . '/apps/views/admin/menu-form.php';
    }

    public function delete(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            MenuService::delete($id);
        }
        redirect('/POS/public/index.php?route=menu');
    }
}

<?php
class ServiceController {
    public function index(): void {
        requireLogin();
        $items = ServiceService::all();
        include APP_ROOT . '/apps/views/admin/services.php';
    }

    public function create(): void {
        requireRole(['admin', 'supervisor']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ServiceService::create([
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Service'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => trim($_POST['price'] ?? 0),
                'image' => trim($_POST['image'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ]);
            redirect('/POS/public/index.php?route=services');
        }
        include APP_ROOT . '/apps/views/admin/service-form.php';
    }

    public function edit(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        $item = Service::findById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ServiceService::update($id, [
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Service'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => trim($_POST['price'] ?? 0),
                'image' => trim($_POST['image'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ]);
            redirect('/POS/public/index.php?route=services');
        }
        include APP_ROOT . '/apps/views/admin/service-form.php';
    }

    public function delete(): void {
        requireRole(['admin', 'supervisor']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            ServiceService::delete($id, []);
        }
        redirect('/POS/public/index.php?route=services');
    }
}

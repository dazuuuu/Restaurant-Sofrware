<?php
class MenuService {
    public static function all(): array {
        return MenuItem::all();
    }

    public static function create(array $data): array {
        return MenuItem::create($data);
    }

    public static function update(int $id, array $data): array {
        return MenuItem::update($id, $data);
    }

    public static function delete(int $id): void {
        MenuItem::delete($id);
    }
}

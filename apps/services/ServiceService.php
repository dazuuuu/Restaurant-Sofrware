<?php
class ServiceService {
    public static function all(): array {
        return Service::all();
    }

    public static function create(array $data): array {
        return Service::create($data);
    }

    public static function update(int $id, array $data): array {
        return Service::update($id, $data);
    }

    public static function delete(int $id, array $data): void {
        Service::delete($id);
    }
}

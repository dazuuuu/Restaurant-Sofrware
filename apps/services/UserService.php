<?php
class UserService {
    public static function create(array $data): array {
        return User::create($data);
    }

    public static function update(int $id, array $data): array {
        return User::update($id, $data);
    }

    public static function delete(int $id): void {
        User::delete($id);
    }
}

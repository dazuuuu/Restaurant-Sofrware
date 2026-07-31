<?php
class SettingsService {
    public static function get(string $key, $default = null) {
        return Settings::get($key, $default);
    }

    public static function set(string $key, $value): void {
        Settings::set($key, $value);
    }

    public static function all(): array {
        return Settings::all();
    }

    public static function update(array $data): void {
        foreach ($data as $key => $value) {
            Settings::set($key, $value);
        }
    }
}

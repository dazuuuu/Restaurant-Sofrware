<?php
class BookingService {
    public static function all(): array {
        return Booking::all();
    }

    public static function create(array $data): array {
        return Booking::create($data);
    }

    public static function update(int $id, array $data): array {
        return Booking::update($id, $data);
    }
}

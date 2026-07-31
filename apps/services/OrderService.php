<?php
class OrderService {
    public static function create(array $data): array {
        return Order::create($data);
    }

    public static function update(int $id, array $data): array {
        return Order::update($id, $data);
    }

    public static function complete(int $id): void {
        Order::completeOrder($id);
    }

    public static function addItem(int $orderId, int $menuItemId, int $quantity, string $instructions = '', ?string $clientId = null): array {
        return Order::addItem($orderId, $menuItemId, $quantity, $instructions, $clientId);
    }

    public static function removeItem(int $itemId): void {
        Order::deleteItem($itemId);
    }

    public static function getItems(int $orderId): array {
        return Order::getOrderItems($orderId);
    }
}

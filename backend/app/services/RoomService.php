<?php

namespace App\Services;

use App\Models\Room;
use PDO;

class RoomService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Room $room): array
    {
        $stmt = $this->pdo->prepare("INSERT INTO rooms (name, capacity, type) VALUES (?, ?, ?)");
        $stmt->execute(params: [$room->getName(), $room->getCapacity(), $room->getType()]);
        
        return ['id' => $this->pdo->lastInsertId(), 'message' => 'Room saved successfully'];
    }

    public function getById(int $id): ?Room
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        return $data ? new Room($data) : null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM rooms");
        $rooms = [];
        
        while ($row = $stmt->fetch()) {
            $rooms[] = new Room($row);
        }
        
        return $rooms;
    }

    public function update(Room $room): Room
    {
        $stmt = $this->pdo->prepare("UPDATE rooms SET name = ?, capacity = ?, type = ? WHERE id = ?");
        $stmt->execute([$room->getName(), $room->getCapacity(), $room->getType(), $room->getId()]);
        
        return $room;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

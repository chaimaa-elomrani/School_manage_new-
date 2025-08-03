<?php
namespace App\Controllers;
use App\Services\RoomService;
use App\Models\Room;
use Core\Db;

class RoomController
{
    private $roomService;
    public function __construct(RoomService $roomService = null)
    {
        if ($roomService) {
            $this->roomService = $roomService;
        } else {
            $pdo = Db::connection();
            $this->roomService = new RoomService($pdo);
        }
    }

    public function create()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $room = new Room($input);
            $result = $this->roomService->save($room);
            echo json_encode(['message' => 'Room created successfully', 'data' => $result]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll()
    {
        try {
            $rooms = $this->roomService->getAll();
            $roomsArray = [];
            foreach ($rooms as $room) {
                $roomsArray[] = $room->toArray();
            }
            echo json_encode(['message' => 'Rooms retrieved successfully', 'data' => $roomsArray]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }


    }

    public function getById($id)
    {
        try {
            $room = $this->roomService->getById($id);
            if ($room) {
                echo json_encode(['message' => 'Room found', 'data' => $room->toArray()]);
            } else {
                echo json_encode(['error' => 'Room not found']);
            }
        } catch (\Exception $e) {

            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        try {
            $input['id'] = $id; // Add the ID to the input data
            $room = new Room($input);
            $result = $this->roomService->update($room);
            echo json_encode(['message' => 'Room updated successfully', 'data' => $result]);

        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $this->roomService->delete($id);
            echo json_encode(['message' => 'Room deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

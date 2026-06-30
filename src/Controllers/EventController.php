<?php
include_once __DIR__ . '/../Repositories/EventRepository.php';
include_once __DIR__ . '/../Services/EventService.php';
class EventController {
    private $service;
    private $db;
    public function __construct($db) {
        $this->db = $db;
        $repository = new EventRepository($db);
        $this->service = new EventService($repository);
    }
    // CHECK QUYỀN
    private function checkManagePermission($userId) {
        $stmt = $this->db->prepare(
            "SELECT role, club_id
             FROM users
             WHERE id = ?"
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (
            !$user ||
            !in_array(
                strtolower(trim($user['role'])),
                ['admin', 'chunhiem']
            )
        ) {
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Bạn không có quyền thực hiện chức năng này!"
            ]);
            exit;
        }
        return $user;
    }

    // TẠO EVENT
    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            echo json_encode([
                "status" => "error",
                "message" => "Dữ liệu JSON trống"
            ]);
            exit;
        }
        // CHECK QUYỀN
        $user = $this->checkManagePermission($data->created_by);
        // ÉP club_id theo user
        $data->club_id = $user['club_id'];
        $result = $this->service->createEvent($data);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    // CHI TIẾT EVENT
    public function detail() {
        $id = $_GET['id'] ?? null;
        $result = $this->service->getEventDetail($id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    // UPDATE EVENT
    public function update() {
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"));
        if (!$id || !$data) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu dữ liệu"
            ]);
            exit;
        }
        $this->checkManagePermission($data->created_by);
        $result = $this->service->updateEvent($id, $data);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    // DELETE EVENT
    public function delete() {
        $id = $_GET['id'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        if (!$id || !$userId) {
            echo json_encode([
                "status" => "error",
                "message" => "Thiếu dữ liệu"
            ]);
            exit;
        }
        $this->checkManagePermission($userId);
        $result = $this->service->deleteEvent($id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    // LIST EVENT
    public function list() {
        $events = $this->service->getAllEvents();
        header('Content-Type: application/json');
        echo json_encode($events);
        exit;
    }
}
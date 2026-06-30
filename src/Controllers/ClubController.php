<?php
class ClubController {
    private $service;

    public function __construct($service) {
        $this->service = $service;
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        $path = $_SERVER['PATH_INFO'] ?? '';
        $uri = explode('/', trim($path, '/'));
        $action = $uri[1] ?? '';

        $method = $_SERVER['REQUEST_METHOD'];

        // 🔹 GET LIST
        if ($action == 'list' && $method == 'GET') {
            echo json_encode($this->service->listAllClubs());
        }

        // 🔹 ADD
        elseif ($action == 'add' && $method == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $this->service->addNewClub($data);
            echo json_encode(["status" => $result ? "success" : "error"]);
        }

        // 🔹 UPDATE
  // 🔹 UPDATE
elseif ($action == 'update' && $method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Lấy id từ URL query string nếu có
    if (isset($_GET['id'])) {
        $data['id'] = $_GET['id'];
    }

    $result = $this->service->updateClub($data);
    
    if ($result) {
        echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Cập nhật thất bại - Kiểm tra dữ liệu"]);
    }
}
        // 🔹 DELETE
        elseif ($action == 'delete' && $method == 'GET') {
            $id = $_GET['id'] ?? null;
            $result = $this->service->deleteClub($id);
            echo json_encode(["status" => $result ? "success" : "error"]);
        }

        else {
            echo json_encode(["status" => "error", "message" => "Invalid API"]);
        }
    }
    
}
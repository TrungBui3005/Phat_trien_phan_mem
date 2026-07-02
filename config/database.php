<?php
class Database {
    private $host = "localhost";
    private $db_name = "clb_management"; 
    private $username = "root";           
    private $password = "";               
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Trả về JSON lỗi thay vì echo chữ thuần
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_with_status(["status" => "error", "message" => "Lỗi kết nối CSDL: " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}

// Hàm bổ trợ viết nhanh chuỗi json nếu chưa có
if (!function_exists('json_with_status')) {
    function json_with_status($data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
?>
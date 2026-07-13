<?php

class Database
{
    // Thông tin kết nối cơ sở dữ liệu
    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'manga_workflow';
    private $username = 'root'; // Sửa lại nếu bạn có cấu hình username khác
    private $password = '';     // Sửa lại nếu bạn có cài đặt password cho MySQL

    // Biến lưu trữ đối tượng kết nối (PDO)
    private $conn;

    /**
     * Khởi tạo và trả về kết nối cơ sở dữ liệu (PDO)
     *
     * @return PDO|null Trả về đối tượng kết nối nếu thành công, null nếu có lỗi
     */
    public function connect()
    {
        // Đảm bảo khởi tạo kết nối rỗng trước mỗi lần gọi
        $this->conn = null;

        try {
            // Xây dựng chuỗi kết nối DSN (Data Source Name)
            // Bao gồm định dạng host, port, dbname và thiết lập charset là utf8mb4 để hỗ trợ Unicode (như tiếng Việt, Emoji)
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->dbname . ";charset=utf8mb4";

            // Khởi tạo đối tượng PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Cấu hình thuộc tính cho PDO
            // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION: Bật chế độ ném ra các Exception khi có lỗi SQL
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // (Tùy chọn thêm để tiện sử dụng) Cấu hình kiểu dữ liệu trả về mặc định là mảng kết hợp (Associative Array)
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Bắt và xử lý ngoại lệ khi có lỗi kết nối
            // Trong môi trường thực tế, nên ghi lỗi này ra file log (error_log) thay vì echo trực tiếp ra màn hình
            echo "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage();
        }

        return $this->conn;
    }
}

<?php
require_once __DIR__ . "/../config/Database.php";

class BaseDAO extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    // Thực thi câu lệnh SELECT
    protected function executeQuery(string $sql): mysqli_result|false
    {
        return $this->conn->query($sql);
    }

    // Chuẩn bị câu lệnh Prepared Statement
    protected function prepare(string $sql): mysqli_stmt|false
    {
        return $this->conn->prepare($sql);
    }

    // Bắt đầu Transaction
    protected function beginTransaction(): void
    {
        $this->conn->begin_transaction();
    }

    // Xác nhận Transaction
    protected function commit(): void
    {
        $this->conn->commit();
    }

    // Hủy Transaction
    protected function rollback(): void
    {
        $this->conn->rollback();
    }

    // Đóng kết nối
    public function close(): void
    {
        $this->conn->close();
    }
}
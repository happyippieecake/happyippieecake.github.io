<?php
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../dashboard.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testQueryDashboard()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM menu");
        $this->assertNotFalse($result);
    }
}

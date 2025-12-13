<?php
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../admin.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testDatabaseConnect()
    {
        $this->assertFalse($this->conn->connect_errno);
    }
}

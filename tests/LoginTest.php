<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../login.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testQueryUser()
    {
        $result = $this->conn->query("SELECT * FROM user LIMIT 1");
        $this->assertNotFalse($result);
    }
}

<?php
use PHPUnit\Framework\TestCase;

class EditMenuTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../edit_menu.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testAmbilMenu()
    {
        $result = $this->conn->query("SELECT * FROM menu LIMIT 1");
        $this->assertNotFalse($result);
    }
}

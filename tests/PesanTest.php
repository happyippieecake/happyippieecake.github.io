<?php
use PHPUnit\Framework\TestCase;

class PesanTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../pesan.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testQueryMenuUntukPemesanan()
    {
        $result = $this->conn->query("SELECT * FROM menu LIMIT 1");
        $this->assertNotFalse($result);
    }
}

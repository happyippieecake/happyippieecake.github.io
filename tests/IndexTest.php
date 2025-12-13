<?php
use PHPUnit\Framework\TestCase;


class IndexTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../index.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testKoneksiKeDatabase()
    {
        $this->assertFalse($this->conn->connect_errno, "Koneksi MySQL gagal");
    }

    public function testQueryMenuBerjalan()
    {
        $result = $this->conn->query("SELECT * FROM menu LIMIT 1");
        $this->assertNotFalse($result);

        $row = $result->fetch_assoc();
        if ($row) {
            $this->assertIsArray($row);
        }
    }
}

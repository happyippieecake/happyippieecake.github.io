<?php
use PHPUnit\Framework\TestCase;

class DataPesananTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        require __DIR__ . '/../data_pesanan.php';

        $this->conn = $conn;
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testQueryPesanan()
    {
        $result = $this->conn->query("SELECT * FROM pesanan LIMIT 1");
        $this->assertNotFalse($result);
    }
}

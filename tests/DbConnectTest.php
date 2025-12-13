<?php
use PHPUnit\Framework\TestCase;

class DbConnectTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        require __DIR__ . '/../db_connect.php';
        $this->conn = $conn;

        // pastikan koneksi valid
        $this->assertInstanceOf(mysqli::class, $this->conn, 
            "Variabel \$conn harus berupa object mysqli"
        );
    }

    public function testKoneksiDatabaseAda()
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testQueryMenuTidakError()
    {
        $result = $this->conn->query("SELECT 1 AS test LIMIT 1");

        $this->assertNotFalse($result, "Query sederhana tidak boleh menghasilkan false");

        $row = $result->fetch_assoc();

        $this->assertArrayHasKey('test', $row);
        $this->assertEquals('1', (string)$row['test']);
    }
}

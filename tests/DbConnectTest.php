<?php
/**
 * Tests for db_connect.php database connection
 */
use PHPUnit\Framework\TestCase;

class DbConnectTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "happyippiecake");
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testConnectionIsEstablished()
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
    }

    public function testConnectionHasNoError()
    {
        $this->assertEquals(0, $this->conn->connect_errno, "Database connection failed");
    }

    public function testDatabaseNameIsCorrect()
    {
        $result = $this->conn->query("SELECT DATABASE() as db");
        $row = $result->fetch_assoc();
        $this->assertEquals('happyippiecake', $row['db']);
    }

    public function testCanExecuteSimpleQuery()
    {
        $result = $this->conn->query("SELECT 1 AS test");
        $this->assertNotFalse($result);
        $row = $result->fetch_assoc();
        $this->assertEquals('1', $row['test']);
    }

    public function testCharacterSetIsUtf8()
    {
        $charset = $this->conn->character_set_name();
        $this->assertStringContainsString('utf8', strtolower($charset));
    }

    public function testCanQueryShowTables()
    {
        $result = $this->conn->query("SHOW TABLES");
        $this->assertNotFalse($result);
        $this->assertGreaterThan(0, $result->num_rows, "Database should have tables");
    }

    public function testMenuTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'menu'");
        $this->assertNotFalse($result);
        $this->assertEquals(1, $result->num_rows, "Table 'menu' should exist");
    }

    public function testPesananTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'pesanan'");
        $this->assertNotFalse($result);
        $this->assertEquals(1, $result->num_rows, "Table 'pesanan' should exist");
    }

    public function testConnectionSupportsTransactions()
    {
        $this->assertTrue($this->conn->begin_transaction());
        $this->assertTrue($this->conn->rollback());
    }

    public function testPreparedStatementsWork()
    {
        $stmt = $this->conn->prepare("SELECT ? AS value");
        $this->assertNotFalse($stmt);
        $value = 'test';
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->assertEquals('test', $row['value']);
    }
}

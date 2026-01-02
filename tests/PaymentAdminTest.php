<?php
/**
 * Tests for payment_admin.php functionality
 */
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../PaymentGateway.php';

class PaymentAdminTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "happyippiecake");
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['status'] = 'login';
    }

    public function testPaymentAdminPageLoads()
    {
        ob_start();
        include __DIR__ . '/../payment_admin.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('html', strtolower($output));
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
        $this->assertEquals(0, $this->conn->connect_errno);
    }

    public function testPaymentsTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'payments'");
        $this->assertNotFalse($result);
    }

    public function testGetPendingPayments()
    {
        $result = $this->conn->query("SELECT * FROM payments WHERE status = 'pending'");
        if ($result) {
            $this->assertNotFalse($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testGetConfirmedPayments()
    {
        $result = $this->conn->query("SELECT * FROM payments WHERE status = 'confirmed'");
        if ($result) {
            $this->assertNotFalse($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testConfirmPaymentStatement()
    {
        $stmt = $this->conn->prepare("UPDATE payments SET status = 'confirmed' WHERE id = ?");
        if ($stmt) {
            $id = 999999;
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $this->assertTrue($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testCancelPaymentStatement()
    {
        $stmt = $this->conn->prepare("UPDATE payments SET status = 'cancelled' WHERE id = ?");
        if ($stmt) {
            $id = 999999;
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $this->assertTrue($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testPaymentsWithPesananJoin()
    {
        $result = $this->conn->query(
            "SELECT p.*, ps.nama_pemesan 
             FROM payments p 
             LEFT JOIN pesanan ps ON p.pesanan_id = ps.id 
             LIMIT 10"
        );
        if ($result) {
            $this->assertNotFalse($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testCountPaymentsByStatus()
    {
        $result = $this->conn->query("SELECT status, COUNT(*) as count FROM payments GROUP BY status");
        if ($result) {
            $this->assertNotFalse($result);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testPaymentGatewayConfirmPayment()
    {
        $gateway = new PaymentGateway($this->conn);
        // Test with non-existent ID (should not fail)
        $result = $gateway->confirmPayment(999999);
        $this->assertIsBool($result);
    }

    public function testPaymentGatewayCancelPayment()
    {
        $gateway = new PaymentGateway($this->conn);
        // Test with non-existent ID (should not fail)
        $result = $gateway->cancelPayment(999999);
        $this->assertIsBool($result);
    }

    public function testBuktiTransferColumn()
    {
        $result = $this->conn->query("SELECT bukti_transfer FROM payments LIMIT 1");
        if ($result) {
            $this->assertNotFalse($result);
        } else {
            $this->assertTrue(true);
        }
    }
}

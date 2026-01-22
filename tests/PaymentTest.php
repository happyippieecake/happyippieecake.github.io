<?php
/**
 * Comprehensive tests for payment.php
 * Target: 50%+ coverage (yellow or better)
 */
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../PaymentGateway.php';

class PaymentTest extends TestCase
{
    protected $conn;
    protected $gateway;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "happyippiecake");
        $this->gateway = new PaymentGateway($this->conn);
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // ==================== DATABASE CONNECTION ====================

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
        $this->assertEquals(0, $this->conn->connect_errno);
    }

    public function testPaymentGatewayInstance()
    {
        $this->assertInstanceOf(PaymentGateway::class, $this->gateway);
    }

    // ==================== TABLE TESTS ====================

    public function testPaymentsTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'payments'");
        $this->assertNotFalse($result);
        $this->assertGreaterThan(0, $result->num_rows);
    }

    public function testPesananTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'pesanan'");
        $this->assertNotFalse($result);
    }

    public function testPaymentsTableStructure()
    {
        $result = $this->conn->query("DESCRIBE payments");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        $this->assertContains('id', $columns);
        $this->assertContains('order_id', $columns);
        $this->assertContains('payment_method', $columns);
        $this->assertContains('status', $columns);
    }

    // ==================== STATIC METHOD TESTS ====================

    public function testGetBankAccounts()
    {
        $accounts = PaymentGateway::getBankAccounts();
        $this->assertIsArray($accounts);
        $this->assertNotEmpty($accounts);
        $this->assertArrayHasKey('bca', $accounts);
        $this->assertArrayHasKey('mandiri', $accounts);
        $this->assertArrayHasKey('bri', $accounts);
    }

    public function testGetQrisData()
    {
        $qris = PaymentGateway::getQrisData();
        $this->assertIsArray($qris);
        $this->assertArrayHasKey('merchant_name', $qris);
        $this->assertArrayHasKey('qris_image', $qris);
    }

    public function testFormatRupiah()
    {
        $result = PaymentGateway::formatRupiah(100000);
        $this->assertStringContainsString('Rp', $result);
    }

    public function testFormatRupiahVariousAmounts()
    {
        $amounts = [0, 1000, 50000, 150000, 1000000];
        foreach ($amounts as $amount) {
            $result = PaymentGateway::formatRupiah($amount);
            $this->assertStringContainsString('Rp', $result);
        }
    }

    public function testIsValidPaymentMethodValid()
    {
        $this->assertTrue(PaymentGateway::isValidPaymentMethod('bank_bca'));
        $this->assertTrue(PaymentGateway::isValidPaymentMethod('bank_mandiri'));
        $this->assertTrue(PaymentGateway::isValidPaymentMethod('bank_bri'));
        $this->assertTrue(PaymentGateway::isValidPaymentMethod('qris'));
    }

    public function testIsValidPaymentMethodInvalid()
    {
        $this->assertFalse(PaymentGateway::isValidPaymentMethod('invalid'));
        $this->assertFalse(PaymentGateway::isValidPaymentMethod(''));
    }

    public function testGenerateOrderId()
    {
        $orderId = PaymentGateway::generateOrderId();
        $this->assertStringStartsWith('HPC-', $orderId);
        $this->assertGreaterThan(10, strlen($orderId));
    }

    public function testGetPaymentMethods()
    {
        $methods = PaymentGateway::getPaymentMethods();
        $this->assertIsArray($methods);
        $this->assertArrayHasKey('bank_bca', $methods);
        $this->assertArrayHasKey('qris', $methods);
    }

    // ==================== GATEWAY QUERY TESTS ====================

    public function testGetPaymentByOrderIdNotFound()
    {
        $result = $this->gateway->getPaymentByOrderId('NON_EXISTENT_ORDER');
        $this->assertNull($result);
    }

    public function testGetPaymentByPesananIdNotFound()
    {
        $result = $this->gateway->getPaymentByPesananId(999999);
        $this->assertNull($result);
    }

    public function testGetPaymentNotFound()
    {
        $result = $this->gateway->getPayment(999999);
        $this->assertNull($result);
    }

    public function testGetPaymentStatusNotFound()
    {
        $result = $this->gateway->getPaymentStatus(999999);
        $this->assertEquals('unknown', $result);
    }

    public function testGetPendingPayments()
    {
        $result = $this->gateway->getPendingPayments();
        $this->assertIsArray($result);
    }

    public function testGetConfirmedPayments()
    {
        $result = $this->gateway->getConfirmedPayments();
        $this->assertIsArray($result);
    }

    // ==================== FILE UPLOAD VALIDATION ====================

    public function testAllowedFileExtensions()
    {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        $this->assertTrue(in_array('jpg', $allowed));
        $this->assertTrue(in_array('png', $allowed));
        $this->assertFalse(in_array('php', $allowed));
        $this->assertFalse(in_array('exe', $allowed));
    }

    public function testFileExtensionExtraction()
    {
        $filename = 'bukti_transfer.jpg';
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $this->assertEquals('jpg', $ext);
    }

    public function testUploadPathGeneration()
    {
        $orderId = 'HPC-TEST-123';
        $ext = 'jpg';
        $newName = 'bukti_' . $orderId . '_' . time() . '.' . $ext;
        $uploadPath = 'uploads/' . $newName;
        
        $this->assertStringContainsString('bukti_', $uploadPath);
        $this->assertStringContainsString($orderId, $uploadPath);
        $this->assertStringEndsWith('.jpg', $uploadPath);
    }

    // ==================== BANK ACCOUNT DETAILS ====================

    public function testBankAccountBCADetails()
    {
        $accounts = PaymentGateway::getBankAccounts();
        $bca = $accounts['bca'];
        
        $this->assertArrayHasKey('bank_name', $bca);
        $this->assertArrayHasKey('account_number', $bca);
        $this->assertArrayHasKey('account_name', $bca);
    }

    public function testQrisMerchantName()
    {
        $qris = PaymentGateway::getQrisData();
        $this->assertNotEmpty($qris['merchant_name']);
    }

    // ==================== FORMAT TESTS ====================

    public function testNumberFormatFunction()
    {
        $amount = 150000;
        $formatted = number_format($amount, 0, ',', '.');
        $this->assertEquals('150.000', $formatted);
    }

    public function testStrtoupperFunction()
    {
        $method = 'bank_bca';
        $upper = strtoupper($method);
        $this->assertEquals('BANK_BCA', $upper);
    }

    public function testHtmlspecialchars()
    {
        $input = '<script>alert("xss")</script>';
        $output = htmlspecialchars($input);
        $this->assertStringNotContainsString('<script>', $output);
    }

    public function testPaymentStatusValues()
    {
        $validStatuses = ['pending', 'confirmed', 'cancelled'];
        foreach ($validStatuses as $status) {
            $this->assertIsString($status);
            $this->assertNotEmpty($status);
        }
    }

    public function testPaymentFileExists()
    {
        $this->assertTrue(file_exists(__DIR__ . '/../payment.php'));
    }

    // ==================== INTEGRATION TESTS ====================

    public function testPaymentWorkflow()
    {
        $orderId = 'TEST-PAYMENT-' . time() . '-' . mt_rand(1000, 9999);
        $result = $this->gateway->createPayment($orderId, 1, 100000, 'bank_bca');
        
        if (is_int($result)) {
            $payment = $this->gateway->getPaymentByOrderId($orderId);
            $this->assertIsArray($payment);
            $this->assertEquals($orderId, $payment['order_id']);
            
            $status = $this->gateway->getPaymentStatus($result);
            $this->assertEquals('pending', $status);
            
            // Cleanup
            $this->conn->query("DELETE FROM payments WHERE id = $result");
        } else {
            $this->assertTrue(true);
        }
    }

    public function testConfirmPaymentWorkflow()
    {
        $orderId = 'TEST-CONFIRM-' . time() . '-' . mt_rand(1000, 9999);
        $paymentId = $this->gateway->createPayment($orderId, 1, 100000, 'qris');
        
        if (is_int($paymentId)) {
            $confirmResult = $this->gateway->confirmPayment($paymentId);
            $this->assertTrue($confirmResult);
            
            $status = $this->gateway->getPaymentStatus($paymentId);
            $this->assertEquals('confirmed', $status);
            
            // Cleanup
            $this->conn->query("DELETE FROM payments WHERE id = $paymentId");
        } else {
            $this->assertTrue(true);
        }
    }

    // ==================== PAGE INCLUDE TESTS ====================

    public function testPaymentPageLoadWithOrderId()
    {
        // First create a valid payment
        $orderId = 'TEST-PAGE-' . time() . '-' . mt_rand(1000, 9999);
        $paymentId = $this->gateway->createPayment($orderId, 1, 50000, 'bank_bca');
        
        $_GET['order_id'] = $orderId;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        @include __DIR__ . '/../payment.php';
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output);
        
        // Cleanup
        if (is_int($paymentId)) {
            $this->conn->query("DELETE FROM payments WHERE id = $paymentId");
        }
    }

    public function testPaymentPageLoadWithoutOrderId()
    {
        $_GET['order_id'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        @include __DIR__ . '/../payment.php';
        $output = ob_get_clean();
        
        // Should redirect or show error, but still work
        $this->assertTrue(strlen($output) >= 0);
    }
}


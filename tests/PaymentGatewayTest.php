<?php
/**
 * Comprehensive tests for PaymentGateway class
 * Target: 80%+ coverage
 */
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../PaymentGateway.php';

class PaymentGatewayTest extends TestCase
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

    // ==================== STATIC METHOD TESTS ====================

    public function testGetBankAccounts()
    {
        $accounts = PaymentGateway::getBankAccounts();
        $this->assertIsArray($accounts);
        $this->assertArrayHasKey('bca', $accounts);
        $this->assertArrayHasKey('mandiri', $accounts);
        $this->assertArrayHasKey('bri', $accounts);
    }

    public function testGetBankAccountDetails()
    {
        $accounts = PaymentGateway::getBankAccounts();
        
        // Test BCA
        $this->assertArrayHasKey('bank_name', $accounts['bca']);
        $this->assertArrayHasKey('account_number', $accounts['bca']);
        $this->assertArrayHasKey('account_name', $accounts['bca']);
        
        // Test Mandiri
        $this->assertArrayHasKey('bank_name', $accounts['mandiri']);
        
        // Test BRI
        $this->assertArrayHasKey('bank_name', $accounts['bri']);
    }

    public function testGetQrisData()
    {
        $qris = PaymentGateway::getQrisData();
        $this->assertIsArray($qris);
        $this->assertArrayHasKey('merchant_name', $qris);
        $this->assertArrayHasKey('qris_image', $qris);
    }

    public function testGenerateOrderId()
    {
        $orderId = PaymentGateway::generateOrderId();
        $this->assertIsString($orderId);
        $this->assertStringStartsWith('HPC-', $orderId);
        $this->assertGreaterThan(10, strlen($orderId));
    }

    public function testGenerateOrderIdUniqueness()
    {
        $id1 = PaymentGateway::generateOrderId();
        usleep(1000); // Small delay
        $id2 = PaymentGateway::generateOrderId();
        $this->assertNotEquals($id1, $id2);
    }

    public function testFormatRupiah()
    {
        $formatted = PaymentGateway::formatRupiah(250000);
        $this->assertStringContainsString('Rp', $formatted);
        $this->assertStringContainsString('250', $formatted);
    }

    public function testFormatRupiahWithDecimals()
    {
        $formatted = PaymentGateway::formatRupiah(1500000);
        $this->assertStringContainsString('Rp', $formatted);
    }

    public function testFormatRupiahZero()
    {
        $formatted = PaymentGateway::formatRupiah(0);
        $this->assertStringContainsString('Rp', $formatted);
    }

    public function testGetPaymentMethods()
    {
        $methods = PaymentGateway::getPaymentMethods();
        $this->assertIsArray($methods);
        $this->assertArrayHasKey('bank_bca', $methods);
        $this->assertArrayHasKey('bank_mandiri', $methods);
        $this->assertArrayHasKey('bank_bri', $methods);
        $this->assertArrayHasKey('qris', $methods);
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
        $this->assertFalse(PaymentGateway::isValidPaymentMethod('bank_xyz'));
    }

    // ==================== INSTANCE METHOD TESTS ====================

    public function testConstructor()
    {
        $gateway = new PaymentGateway($this->conn);
        $this->assertInstanceOf(PaymentGateway::class, $gateway);
    }

    public function testConstructorWithNullConnection()
    {
        $gateway = new PaymentGateway(null);
        $this->assertInstanceOf(PaymentGateway::class, $gateway);
    }

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

    // ==================== BANK ACCOUNT VALIDATION ====================

    public function testBankAccountNumbersAreStrings()
    {
        $accounts = PaymentGateway::getBankAccounts();
        foreach ($accounts as $bank => $details) {
            $this->assertIsString($details['account_number'], "Account number for $bank should be string");
        }
    }

    public function testBankNamesAreNotEmpty()
    {
        $accounts = PaymentGateway::getBankAccounts();
        foreach ($accounts as $bank => $details) {
            $this->assertNotEmpty($details['bank_name'], "Bank name for $bank should not be empty");
        }
    }

    // ==================== QRIS DATA VALIDATION ====================

    public function testQrisMerchantName()
    {
        $qris = PaymentGateway::getQrisData();
        $this->assertNotEmpty($qris['merchant_name']);
    }

    public function testQrisImagePath()
    {
        $qris = PaymentGateway::getQrisData();
        $this->assertNotEmpty($qris['qris_image']);
    }

    // ==================== ORDER ID FORMAT ====================

    public function testOrderIdFormat()
    {
        $orderId = PaymentGateway::generateOrderId();
        
        // Should start with HPC-
        $this->assertMatchesRegularExpression('/^HPC-/', $orderId);
        
        // Should contain timestamp
        $this->assertMatchesRegularExpression('/\d{14}/', $orderId);
    }

    public function testMultipleOrderIdsAreUnique()
    {
        $ids = [];
        for ($i = 0; $i < 5; $i++) {
            $ids[] = PaymentGateway::generateOrderId();
            usleep(100);
        }
        
        $uniqueIds = array_unique($ids);
        $this->assertCount(count($ids), $uniqueIds, "All order IDs should be unique");
    }

    // ==================== CURRENCY FORMATTING ====================

    public function testCurrencyFormattingVariousAmounts()
    {
        $testCases = [
            100 => 'Rp',
            1000 => 'Rp',
            10000 => 'Rp',
            100000 => 'Rp',
            1000000 => 'Rp',
        ];

        foreach ($testCases as $amount => $expected) {
            $result = PaymentGateway::formatRupiah($amount);
            $this->assertStringContainsString($expected, $result);
        }
    }
}

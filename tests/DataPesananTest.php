<?php
/**
 * Integration tests for data_pesanan.php functionality
 */
use PHPUnit\Framework\TestCase;

class DataPesananTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['status'] = 'login';
    }

    public function testDataPesananPageLoads()
    {
        ob_start();
        include __DIR__ . '/../data_pesanan.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('html', strtolower($output));
        $this->assertStringContainsString('Data Pesanan', $output);
    }
}

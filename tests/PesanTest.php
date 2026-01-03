<?php
/**
 * Integration tests for pesan.php functionality
 */
use PHPUnit\Framework\TestCase;

class PesanTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset superglobals
        $_POST = [];
        $_GET = [];
    }

    public function testPesanPageLoads()
    {
        ob_start();
        include __DIR__ . '/../pesan.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('html', strtolower($output));
        $this->assertStringContainsString('Form Pemesanan', $output);
    }
}

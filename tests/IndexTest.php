<?php
/**
 * Integration tests for index.php using output buffering
 * Tests the actual HTML output of the page
 */
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
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

    public function testIndexPageOutputsHtml()
    {
        // Suppress output
        ob_start();
        
        // Simulate global variable if needed (db_connect.php creates $conn)
        // We don't need to define $conn here because index.php requires db_connect.php
        // But we might want to capture it to avoid 'variable undefined' notices if we were mocking
        
        // Prevent 'headers already sent' error if index.php sends headers
        // Since we are CLI, headers are problematic only if checking them.
        
        // Include the file
        include __DIR__ . '/../index.php';
        
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('HappyippieCake', $output);
    }
}

<?php
/**
 * Integration tests for login.php functionality
 */
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        $_POST = [];
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Ensure logged out
        unset($_SESSION['status']);
    }

    public function testLoginPageLoads()
    {
        ob_start();
        include __DIR__ . '/../login.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('html', strtolower($output));
        // Should contain input for username/password
        $this->assertStringContainsString('username', strtolower($output));
        $this->assertStringContainsString('password', strtolower($output));
    }
}

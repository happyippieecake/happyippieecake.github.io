<?php
/**
 * Integration tests for admin.php functionality
 */
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{
    protected function setUp(): void
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Simulate logged in admin
        $_SESSION['status'] = 'login';
    }

    public function testAdminPageRuns()
    {
        ob_start();
        include __DIR__ . '/../admin.php';
        $output = ob_get_clean();
        
        // Should contain admin specific content or redirection if failed
        // Since we set session login, it should show dashboard/admin content
        $this->assertNotEmpty($output);
        // Admin page usually has "Dashboard" or "Kelola Menu"
        $this->assertStringContainsString('html', strtolower($output));
    }
}

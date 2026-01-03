<?php
/**
 * Integration tests for dashboard.php functionality
 */
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['status'] = 'login';
    }

    public function testDashboardPageLoads()
    {
        ob_start();
        include __DIR__ . '/../dashboard.php';
        $output = ob_get_clean();
        
        $this->assertStringContainsString('html', strtolower($output));
        $this->assertStringContainsString('Dashboard', $output);
    }
}

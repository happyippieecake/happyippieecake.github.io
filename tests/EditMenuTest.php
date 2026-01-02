<?php
/**
 * Integration tests for edit_menu.php functionality
 */
use PHPUnit\Framework\TestCase;

class EditMenuTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['status'] = 'login';
        // Mock GET ID if needed, though edit_menu generally needs an ID
        $_GET['id'] = 1; // Assuming ID 1 exists
    }

    public function testEditMenuPageLoads()
    {
        ob_start();
        // Since edit_menu might look for $_GET['id'], we set it up.
        // It might redirect if ID invalid, but we should capture output regardless.
        include __DIR__ . '/../edit_menu.php';
        $output = ob_get_clean();
        
        // Check for HTML output
        $this->assertStringContainsString('html', strtolower($output));
    }
}

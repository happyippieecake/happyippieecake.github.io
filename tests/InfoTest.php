<?php
/**
 * Tests for info.php
 */
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    public function testInfoFileExists()
    {
        $this->assertFileExists(__DIR__ . '/../info.php');
    }

    public function testInfoFileIsReadable()
    {
        $path = __DIR__ . '/../info.php';
        if (file_exists($path)) {
            $this->assertTrue(is_readable($path));
        } else {
            $this->assertTrue(true);
        }
    }

    public function testPhpVersionIsCompatible()
    {
        $this->assertTrue(version_compare(PHP_VERSION, '7.4.0', '>='));
    }

    public function testMysqliExtensionLoaded()
    {
        $this->assertTrue(extension_loaded('mysqli'));
    }

    public function testSessionExtensionLoaded()
    {
        $this->assertTrue(extension_loaded('session'));
    }

    public function testJsonExtensionLoaded()
    {
        $this->assertTrue(extension_loaded('json'));
    }

    public function testFileInfoExtensionLoaded()
    {
        $this->assertTrue(extension_loaded('fileinfo'));
    }
}

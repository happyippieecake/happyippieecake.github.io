<?php
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    public function testInfoFileAda()
    {
        $this->assertFileExists(__DIR__ . '/../info.php');
    }
}

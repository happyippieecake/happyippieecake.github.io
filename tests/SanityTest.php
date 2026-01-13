<?php
use PHPUnit\Framework\TestCase;

class SanityTest extends TestCase
{
    public function testBasicMath()
    {
        $this->assertTrue(1 + 1 === 2);
    }
}

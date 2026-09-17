<?php

namespace Ttpryg\AuthUser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\AuthUser\Security\NativePasswordHasher;

class NativePasswordHasherTest extends TestCase
{
    private NativePasswordHasher $hasher;

    protected function setUp(): void
    {
        $this->hasher = new NativePasswordHasher();
    }

    public function testHashAndVerify(): void
    {
        $password = 'Secret123!';
        $hash = $this->hasher->hash($password);

        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue($this->hasher->verify($password, $hash));
        $this->assertFalse($this->hasher->verify('WrongPassword', $hash));
    }

    public function testNeedsRehash(): void
    {
        $password = 'Secret123!';
        $hash = $this->hasher->hash($password);

        $this->assertFalse($this->hasher->needsRehash($hash));
    }
}

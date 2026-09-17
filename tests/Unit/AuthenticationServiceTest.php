<?php

namespace Ttpryg\AuthUser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\AuthUser\Contracts\PasswordHasherInterface;
use Ttpryg\AuthUser\Contracts\UserRepositoryInterface;
use Ttpryg\AuthUser\Entities\User;
use Ttpryg\AuthUser\Exceptions\InvalidCredentialsException;
use Ttpryg\AuthUser\Exceptions\UserInactiveException;
use Ttpryg\AuthUser\Services\AuthenticationService;

class AuthenticationServiceTest extends TestCase
{
    // POSITIVE CASE
    public function testSuccessfulAuthentication(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('user@example.com', 'hashed_pass', 'username', true, [], 1);
        $repo->method('findByEmail')->with('user@example.com')->willReturn($user);

        $hasher->method('verify')->with('password123', 'hashed_pass')->willReturn(true);
        $hasher->method('needsRehash')->willReturn(false);

        $service = new AuthenticationService($repo, $hasher);
        $result = $service->authenticate('user@example.com', 'password123');

        $this->assertEquals($user, $result);
    }

    // NEGATIVE CASE: User Not Found
    public function testAuthenticationFailsOnNonExistentUser(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $repo->method('findByEmail')->with('unknown@example.com')->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $service = new AuthenticationService($repo, $hasher);
        $service->authenticate('unknown@example.com', 'password123');
    }

    // NEGATIVE CASE: Invalid Password
    public function testAuthenticationFailsOnWrongPassword(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('user@example.com', 'hashed_pass', 'username', true, [], 1);
        $repo->method('findByEmail')->with('user@example.com')->willReturn($user);

        $hasher->method('verify')->with('wrong_pass', 'hashed_pass')->willReturn(false);

        $this->expectException(InvalidCredentialsException::class);

        $service = new AuthenticationService($repo, $hasher);
        $service->authenticate('user@example.com', 'wrong_pass');
    }

    // NEGATIVE CASE: Deactivated Account
    public function testAuthenticationFailsOnInactiveUser(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('user@example.com', 'hashed_pass', 'username', false, [], 1);
        $repo->method('findByEmail')->with('user@example.com')->willReturn($user);

        $hasher->method('verify')->with('password123', 'hashed_pass')->willReturn(true);

        $this->expectException(UserInactiveException::class);

        $service = new AuthenticationService($repo, $hasher);
        $service->authenticate('user@example.com', 'password123');
    }
}

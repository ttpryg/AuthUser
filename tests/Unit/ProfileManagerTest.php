<?php

namespace Ttpryg\AuthUser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\AuthUser\Contracts\EventDispatcherInterface;
use Ttpryg\AuthUser\Contracts\PasswordHasherInterface;
use Ttpryg\AuthUser\Contracts\UserRepositoryInterface;
use Ttpryg\AuthUser\Entities\User;
use Ttpryg\AuthUser\Events\UserStatusChangedEvent;
use Ttpryg\AuthUser\Exceptions\InvalidCredentialsException;
use Ttpryg\AuthUser\Exceptions\UserAlreadyExistsException;
use Ttpryg\AuthUser\Exceptions\UserNotFoundException;
use Ttpryg\AuthUser\Services\ProfileManager;

class ProfileManagerTest extends TestCase
{
    // POSITIVE CASE: Update Profile
    public function testSuccessfulProfileUpdate(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('old@example.com', 'hash', 'oldname', true, [], 1);
        $repo->method('findById')->with(1)->willReturn($user);
        $repo->method('findByEmail')->with('new@example.com')->willReturn(null);
        $repo->method('update')->willReturn(true);

        $service = new ProfileManager($repo, $hasher);
        $result = $service->updateProfile(1, email: 'new@example.com');

        $this->assertTrue($result);
        $this->assertEquals('new@example.com', $user->getEmail());
    }

    // NEGATIVE CASE: Update Email already taken by another user
    public function testUpdateProfileFailsWhenEmailTaken(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user1 = new User('user1@example.com', 'hash', 'user1', true, [], 1);
        $user2 = new User('taken@example.com', 'hash', 'user2', true, [], 2);

        $repo->method('findById')->with(1)->willReturn($user1);
        $repo->method('findByEmail')->with('taken@example.com')->willReturn($user2);

        $this->expectException(UserAlreadyExistsException::class);

        $service = new ProfileManager($repo, $hasher);
        $service->updateProfile(1, email: 'taken@example.com');
    }

    // POSITIVE CASE: Change Password
    public function testSuccessfulPasswordChange(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('user@example.com', 'old_hash', 'user', true, [], 1);
        $repo->method('findById')->with(1)->willReturn($user);

        $hasher->method('verify')->with('OldPassword123', 'old_hash')->willReturn(true);
        $hasher->method('hash')->with('NewPassword123')->willReturn('new_hash');
        $repo->method('update')->willReturn(true);

        $service = new ProfileManager($repo, $hasher);
        $result = $service->changePassword(1, 'OldPassword123', 'NewPassword123');

        $this->assertTrue($result);
        $this->assertEquals('new_hash', $user->getPasswordHash());
    }

    // NEGATIVE CASE: Change Password with Wrong Current Password
    public function testChangePasswordFailsOnWrongCurrentPassword(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $user = new User('user@example.com', 'old_hash', 'user', true, [], 1);
        $repo->method('findById')->with(1)->willReturn($user);

        $hasher->method('verify')->with('WrongOldPassword', 'old_hash')->willReturn(false);

        $this->expectException(InvalidCredentialsException::class);

        $service = new ProfileManager($repo, $hasher);
        $service->changePassword(1, 'WrongOldPassword', 'NewPassword123');
    }

    // POSITIVE CASE: Toggle Status and Dispatch Event
    public function testToggleStatusDispatchesEvent(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $user = new User('user@example.com', 'hash', 'user', true, [], 1);
        $repo->method('findById')->with(1)->willReturn($user);
        $repo->method('update')->willReturn(true);

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserStatusChangedEvent::class));

        $service = new ProfileManager($repo, $hasher, $dispatcher);
        $result = $service->toggleStatus(1, false);

        $this->assertTrue($result);
        $this->assertFalse($user->isActive());
    }

    // NEGATIVE CASE: User Not Found
    public function testOperationsFailOnNonExistentUser(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $repo->method('findById')->with(999)->willReturn(null);

        $this->expectException(UserNotFoundException::class);

        $service = new ProfileManager($repo, $hasher);
        $service->updateProfile(999, email: 'any@example.com');
    }
}

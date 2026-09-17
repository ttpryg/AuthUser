<?php

namespace Ttpryg\AuthUser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\AuthUser\Contracts\EventDispatcherInterface;
use Ttpryg\AuthUser\Contracts\PasswordHasherInterface;
use Ttpryg\AuthUser\Contracts\UserRepositoryInterface;
use Ttpryg\AuthUser\Entities\User;
use Ttpryg\AuthUser\Events\UserRegisteredEvent;
use Ttpryg\AuthUser\Exceptions\UserAlreadyExistsException;
use Ttpryg\AuthUser\Services\RegistrationService;

class RegistrationServiceTest extends TestCase
{
    public function testSuccessfulRegistration(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $repo->method('findByEmail')->willReturn(null);
        $repo->method('findByUsername')->willReturn(null);

        $hasher->expects($this->once())
            ->method('hash')
            ->with('Secret123!')
            ->willReturn('hashed_secret');

        $repo->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (User $user) {
                $user->setId(10);
                return $user;
            });

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserRegisteredEvent::class));

        $service = new RegistrationService($repo, $hasher, $dispatcher);
        $user = $service->register('newuser@example.com', 'Secret123!', 'newuser');

        $this->assertEquals(10, $user->getId());
        $this->assertEquals('newuser@example.com', $user->getEmail());
        $this->assertEquals('newuser', $user->getUsername());
        $this->assertEquals('hashed_secret', $user->getPasswordHash());
    }

    public function testRegistrationThrowsExceptionIfEmailExists(): void
    {
        $repo = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(PasswordHasherInterface::class);

        $existingUser = new User('existing@example.com', 'hash', 'existing');
        $repo->method('findByEmail')->with('existing@example.com')->willReturn($existingUser);

        $this->expectException(UserAlreadyExistsException::class);

        $service = new RegistrationService($repo, $hasher);
        $service->register('existing@example.com', 'Secret123!');
    }
}

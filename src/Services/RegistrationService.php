<?php

namespace Ttpryg\AuthUser\Services;

use Ttpryg\AuthUser\Contracts\EventDispatcherInterface;
use Ttpryg\AuthUser\Contracts\PasswordHasherInterface;
use Ttpryg\AuthUser\Contracts\UserRepositoryInterface;
use Ttpryg\AuthUser\Entities\User;
use Ttpryg\AuthUser\Events\UserRegisteredEvent;
use Ttpryg\AuthUser\Exceptions\UserAlreadyExistsException;

class RegistrationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private ?EventDispatcherInterface $eventDispatcher = null
    ) {}

    public function register(
        string $email,
        string $plainPassword,
        ?string $username = null,
        array $metadata = [],
        bool $isActive = true
    ): User {
        // Validate email uniqueness
        if ($this->userRepository->findByEmail($email) !== null) {
            throw UserAlreadyExistsException::forEmail($email);
        }

        // Validate username uniqueness if provided
        if ($username !== null && $this->userRepository->findByUsername($username) !== null) {
            throw UserAlreadyExistsException::forUsername($username);
        }

        // Hash password
        $passwordHash = $this->passwordHasher->hash($plainPassword);

        // Create User entity
        $user = new User(
            email: $email,
            passwordHash: $passwordHash,
            username: $username,
            isActive: $isActive,
            metadata: $metadata
        );

        // Save to database
        $savedUser = $this->userRepository->save($user);

        // Dispatch Event
        $this->eventDispatcher?->dispatch(new UserRegisteredEvent($savedUser));

        return $savedUser;
    }
}

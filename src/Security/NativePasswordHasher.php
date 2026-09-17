<?php

namespace Ttpryg\AuthUser\Security;

use Ttpryg\AuthUser\Contracts\PasswordHasherInterface;

class NativePasswordHasher implements PasswordHasherInterface
{
    private string|int $algo;
    private array $options;

    public function __construct(string|int $algo = PASSWORD_BCRYPT, array $options = [])
    {
        $this->algo = $algo;
        $this->options = $options;
    }

    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, $this->algo, $this->options);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, $this->algo, $this->options);
    }
}

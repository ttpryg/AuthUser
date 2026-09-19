<?php

namespace Ttpryg\AuthUser\Contracts;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, string $hashedPassword): bool;

    public function needsRehash(string $hashedPassword): bool;
}

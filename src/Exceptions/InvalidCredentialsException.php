<?php

namespace Ttpryg\AuthUser\Exceptions;

class InvalidCredentialsException extends AuthUserException
{
    public function __construct(string $message = "Invalid email/username or password.")
    {
        parent::__construct($message);
    }
}

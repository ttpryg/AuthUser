<?php

namespace Ttpryg\AuthUser\Exceptions;

class TokenInvalidException extends AuthUserException
{
    public function __construct(string $message = "The token is invalid or has expired.")
    {
        parent::__construct($message);
    }
}

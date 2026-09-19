<?php

namespace Ttpryg\AuthUser\Exceptions;

class UserInactiveException extends AuthUserException
{
    public function __construct(string $message = 'The user account is deactivated.')
    {
        parent::__construct($message);
    }
}

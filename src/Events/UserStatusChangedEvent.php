<?php

namespace Ttpryg\AuthUser\Events;

use Ttpryg\AuthUser\Entities\User;

class UserStatusChangedEvent
{
    public function __construct(
        public readonly User $user,
        public readonly bool $previousState,
        public readonly bool $newState
    ) {}
}

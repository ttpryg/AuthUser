<?php

namespace Ttpryg\AuthUser\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}

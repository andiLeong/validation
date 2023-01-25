<?php

namespace Andileong\Validation\Rules;

class Ip extends Rule
{
    public function check(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_IP);
    }

    public function message(): string
    {
        return "The $this->key must be valid ip address";
    }
}

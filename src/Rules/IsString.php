<?php

namespace Andileong\Validation\Rules;

class IsString extends Rule
{
    public function check(): bool
    {
        return is_string($this->value);
    }

    public function message(): string
    {
        return "The $this->key must be valid string";
    }
}

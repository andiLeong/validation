<?php

namespace Andileong\Validation\Rules;

class IsArray extends Rule
{
    public function check(): bool
    {
        return is_array($this->value);
    }

    public function message(): string
    {
        return "The $this->key must be valid array";
    }
}

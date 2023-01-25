<?php

namespace Andileong\Validation\Rules;

class AlphaNum extends Rule
{
    public function check(): bool
    {
        return is_string($this->value) && !preg_match('/[^a-z0-9]/i', $this->value);
    }

    public function message(): string
    {
        return "The $this->key must be alpha and number";
    }
}

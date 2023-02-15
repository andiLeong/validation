<?php

namespace Andileong\Validation\Rules;

class Timestamp extends Rule
{
    public function check(): bool
    {
        $value = $this->value;
        return is_integer($value) && $value > 0 && $value < PHP_INT_MAX;
    }

    public function message(): string
    {
        return "The $this->key must be valid timestamp";
    }
}

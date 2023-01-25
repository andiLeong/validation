<?php

namespace Andileong\Validation\Rules;

class Boolean extends Rule
{
    public function check(): bool
    {
        $boolean = [true, false, 0, 1, '0', '1'];
        return in_array($this->value, $boolean, true);
    }

    public function message(): string
    {
        return "The $this->key must be boolean";
    }
}

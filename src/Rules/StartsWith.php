<?php

namespace Andileong\Validation\Rules;

class StartsWith extends Rule
{

    public function check(): bool
    {
        if (!is_string($this->value)) {
            return false;
        }

        return str_starts_with($this->value, $this->arguments[0]);
    }

    public function message(): string
    {
        return "The $this->key must starts with {$this->arguments[0]}";
    }
}

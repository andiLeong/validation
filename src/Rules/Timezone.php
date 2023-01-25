<?php

namespace Andileong\Validation\Rules;

class Timezone extends Rule
{
    public function check(): bool
    {
        return is_string($this->value) && in_array($this->value,timezone_identifiers_list());
    }

    public function message(): string
    {
        return "The $this->key must be valid php timezone";
    }
}

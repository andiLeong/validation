<?php

namespace Andileong\Validation\Rules;

use Andileong\Validation\Trait\EmptyValueChecker;

class Required extends Rule
{
    use EmptyValueChecker;

    public function check(): bool
    {
        return $this->isNotEmpty($this->value);
    }

    public function message(): string
    {
        return "The $this->key is required";
    }
}

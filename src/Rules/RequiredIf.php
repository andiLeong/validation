<?php

namespace Andileong\Validation\Rules;

use Andileong\Validation\Trait\EmptyValueChecker;

class RequiredIf extends Rule
{
    use EmptyValueChecker;

    public function check(): bool
    {
        if($this->otherFieldIsNotEmpty() && $this->isEmpty($this->value)){
           return false;
        }

        return true;
    }

    public function message(): string
    {
        return "The $this->key is required";
    }

    protected function otherFieldIsNotEmpty()
    {
        return $this->isNotEmpty($this->getValue($this->arguments[0]));
    }
}

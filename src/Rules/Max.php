<?php

namespace Andileong\Validation\Rules;

class Max extends Rule
{
    private $max;

    public function __construct($key, $value, $arguments)
    {
        parent::__construct($key, $value, $arguments);
        $this->max = $arguments[0];
    }

    public function check(): bool
    {
        if(is_string($this->value) && strlen(trim($this->value) ?? '') <= $this->max){
            return true;
        }

        if(is_array($this->value)){
            return count($this->value) <= $this->max;
        }

        return false;
    }

    public function message(): string
    {
        return "The $this->key must not exceed $this->max long";
    }
}

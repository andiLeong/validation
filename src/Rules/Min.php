<?php

namespace Andileong\Validation\Rules;

class Min extends Rule
{
    private $min;

    public function __construct($key, $value, $arguments)
    {
        parent::__construct($key, $value, $arguments);
        $this->min = $arguments[0];
    }

    public function check(): bool
    {
        if(is_string($this->value) && strlen(trim($this->value) ?? '') >= $this->min){
            return true;
        }

        if(is_array($this->value)){
            return count($this->value) >= $this->min;
        }

        return false;
    }

    public function message() :string
    {
        return "The $this->key must at least be $this->min long";
    }


}

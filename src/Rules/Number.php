<?php

namespace Andileong\Validation\Rules;

class Number extends Rule
{
    private $positive = false;

    public function __construct($key, $value, $arguments)
    {
        parent::__construct($key, $value, $arguments);
        if(isset($arguments[0]) && $arguments[0] == 'positive'){
            $this->positive = true;
        }
    }

    public function check(): bool
    {
        if($this->positive){
            return is_integer($this->value) && $this->value >= 0;
        }

        return is_integer($this->value);
    }

    public function message(): string
    {
        if($this->positive){
            return "The $this->key must be a positive number";
        }

        return "The $this->key must be a number";
    }
}

<?php

namespace Andileong\Validation\Rules;


class Json extends Rule
{
    public function check(): bool
    {
        return is_string($this->value) && $this->validJson();
    }

    public function message(): string
    {
        return "The $this->key must be valid JSON";
    }

    private function validJson()
    {
        json_decode($this->value);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

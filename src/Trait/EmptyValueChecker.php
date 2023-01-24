<?php

namespace Andileong\Validation\Trait;

trait EmptyValueChecker
{
    public function isEmpty($value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value) && trim($value) == '') {
            return true;
        }

        if (is_array($value)) {
            return empty($value);
        }

        return false;
    }

    public function isNotEmpty($value)
    {
        return !$this->isEmpty($value);
    }
}
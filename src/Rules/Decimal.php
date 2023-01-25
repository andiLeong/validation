<?php

namespace Andileong\Validation\Rules;

class Decimal extends Rule
{
    private $between = [];

    public function __construct($key, $value, $arguments)
    {
        parent::__construct($key, $value, $arguments);
        if (!empty($arguments)) {
            $this->between = count($arguments) == 2
                ? $arguments
                : [1, $arguments[0]];
        }
    }

    public function check(): bool
    {
        if (!is_float($this->value)) {
            return false;
        }

        return !$this->needsToCheckDecimal() || $this->validateDecimals();
    }

    public function message(): string
    {
        return "The $this->key must be valid floating decimal";
    }

    /**
     * determine if need to check decimals
     * @return bool
     */
    protected function needsToCheckDecimal()
    {
        return ! empty($this->between);
    }

    /**
     * get the actual decimals length
     * @return int
     */
    protected function getDecimalsLength()
    {
        return strlen(explode('.', $this->value)[1]);
    }

    /**
     * validate decimals
     * @return bool
     */
    protected function validateDecimals()
    {
        $decimalsLength = $this->getDecimalsLength();
        return $decimalsLength >= $this->between[0] && $decimalsLength <= $this->between[1];
    }
}

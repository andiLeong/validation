<?php

namespace Andileong\Validation\Rules;

use ReflectionClass;

abstract class Rule
{
    protected mixed $value;

    public function __construct(
        protected $key,
        protected $data,
        protected $arguments = []
    )
    {
        $this->value = $this->getValue();
    }

    public function setProperty($data, $key)
    {
        $this->data = $data;
        $this->key = $key;
        $this->value = $this->getValue();

        return $this;
    }

    public function key()
    {
        return $this->key;
    }

    public function getArguments()
    {
       return $this->arguments;
    }

    public function getValue($key = null)
    {
        $key ??= $this->key;
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
//        return Arr::get($this->data, $key ?? $this->key);
    }

    public function getBaseName()
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->isAnonymous()) {
            return 'closure';
        }

        $className = $reflection->getShortName();
        return $this->toSnake($className);
    }

    /**
     * convert a class base name to snake case
     * @param string $className
     * @return string
     */
    private function toSnake(string $className)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    /**
     * get error message of a rule
     * @param array $customMessage
     * @return mixed|string
     */
    public function getErrorMessages(array $customMessage): mixed
    {
        $rule = $this->getBaseName();
        $messageKey = $this->key() . "." . $rule;
        if (array_key_exists($messageKey, $customMessage)) {
            return $this->parseErrorMessage($customMessage[$messageKey]);
        }

        if (array_key_exists($rule, $customMessage)) {
            return $this->parseErrorMessage($customMessage[$rule]);
        }

        return $this->message();
    }


    private function parseErrorMessage(string $rule)
    {
        $argument = implode(',', $this->arguments);
        return str_replace(
            [':key', ':value', ':argument'],
            [$this->key, $this->value, $argument],
            $rule
        );
    }

    abstract public function check(): bool;

    abstract public function message(): string;


}

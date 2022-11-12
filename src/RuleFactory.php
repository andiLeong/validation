<?php

namespace Andileong\Validation;

use Andileong\Validation\Rules\Rule;

class RuleFactory
{
    /**
     * the name of the rule we are trying to build instance
     * @param $name
     *
     * the key of the validation checking
     * @param $key
     *
     * the data getting from the request
     * @param $data
     *
     */

    public function __construct(
        private $name,
        private $key,
        private $data
    )
    {
        //
    }

    /**
     * get a rule instance back
     * @return Rule
     */
    public function make(): Rule
    {
        [$rule, $arguments] = $this->parseClassAndArguments();

        $class = 'Andileong\\Validation\\Rules\\' . $this->convertToRuleClassName($rule);
        return new $class($this->key, $this->data, $arguments);
    }

    /**
     * instantiate an anonymous rule object
     * @return Rule
     */
    public function makeAnonymous()
    {
        $class = new class($this->key, $this->data) extends Rule {
            public $closure;

            public function check(): bool
            {
                $parameters = [$this->getValue(), $this->key(), $this->data];
                return call_user_func($this->closure, ...$parameters);
            }

            public function message(): string
            {
                return "The $this->key is invalid";
            }
        };

        $class->closure = $this->name;
        return $class;
    }

    /**
     * get all arguments a rule object needed after the , symbol
     * 'required|in:1,2,3,4'
     * @return string[]
     */
    public function getArguments()
    {
        $rule = array_reverse(explode( ':',$this->name, 2))[0];
        return explode(',', $rule);
    }

    /**
     * get a class name and its additional arguments
     * @return array
     */
    private function parseClassAndArguments(): array
    {
        $rule = $this->getRuleNameBeforeColon();
        if ($rule === false) {
            return [$this->name, []];
        }

        return [
            $rule,
            $this->getArguments()
        ];
    }


    /**
     * get a rule class name before the colon
     * @return false|string
     */
    protected function getRuleNameBeforeColon()
    {
        return strstr($this->name, ':', true);
    }

    /**
     * convert a rule name to an appropriate rule class name
     * @param $rule
     * @return string
     */
    protected function convertToRuleClassName($rule): string
    {
        $rule = implode('',array_map(fn($value) => ucfirst($value),explode('_',$rule)));
        return $rule;
    }
}

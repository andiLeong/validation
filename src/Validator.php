<?php

namespace Andileong\Validation;

use Andileong\Validation\Rules\Rule;
use Closure;

class Validator
{
    private $messages;
    private $hasErrors = false;
    private $nullable = [];

    public function __construct(
        public array $data,
    )
    {
        //
    }

    /**
     * @throws ValidationException
     */
    public function validate(array $rules, array $message = [])
    {
        $this->messages = $message;
        $results = array_map(fn($rule, $key) => $this->createRule(
            $this->parseRuleToArray($rule), $key
        ), $rules, array_keys($rules));

        $flattenRules = $this->flattenRules($results);

        $results = array_reduce($flattenRules, function ($carry, Rule $rule) {
            $result = $rule->check();
            if (!$result) {
                $this->hasErrors = true;
                $carry['errors'][$rule->key()][] = $rule->getErrorMessages($this->messages);
            }
            return $carry;
        }, []);

        if ($this->hasErrors) {
            throw new ValidationException($results);
        }

        return $this->validatedData($rules);
    }

    /**
     * create a rule collection that contains a rule instance
     * @param array $rules
     * @param $key
     * @return array
     */
    private function createRule(array $rules, $key): array
    {
        $rulesArray = [];
        foreach ($rules as $value){
            if($value === 'nullable'){
                $this->nullable[] = $key;
                continue;
            }
            $this->buildRuleInstance($value, $key);
        }

        return $rulesArray;
    }

    /**
     * @param $rule
     * @param $key
     * @return RuleFactory
     */
    protected function getRuleFactory($rule, $key): RuleFactory
    {
        return new RuleFactory($rule, $key, $this->data);
    }

    /**
     * convert a string rule to an array
     * @param $rule
     * @return false|mixed|string[]
     */
    protected function parseRuleToArray($rule)
    {
        if (is_string($rule)) {
            $rule = explode('|', $rule);
        }
        return $rule;
    }

    /**
     * @param $rule
     * @param $key
     * @return Rule
     */
    protected function buildRuleInstance($rule, $key)
    {
        if ($rule instanceof Rule) {
            $rule->setProperty($this->data, $key);
            return $rule;
        }

        $method = $rule instanceof Closure ? 'makeAnonymous' : 'make';

        return $this
            ->getRuleFactory($rule, $key)
            ->$method();
    }

    /**
     * flatten the rules collection
     * @param array $results
     * @return array
     */
    private function flattenRules(array $results)
    {
        $flattened = [];
        array_walk_recursive($results, function ($a) use (&$flattened) {
            $flattened[] = $a;
        });
        return $flattened;
    }

    /**
     * return the validated data
     * @param $rules
     * @return array
     */
    private function validatedData($rules)
    {
        return array_intersect_key($this->data, array_flip(array_keys($rules)));
    }

}

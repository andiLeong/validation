<?php

namespace Tests;

use Andileong\Validation\RuleFactory;
use Andileong\Validation\Rules\Custom;
use Andileong\Validation\Rules\In;
use Andileong\Validation\Rules\Required;
use Andileong\Validation\ValidationException;
use Andileong\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidationRuleTest extends testcase
{
    /** @test */
    public function it_can_get_a_rule_instance()
    {
        $factory = new RuleFactory('required', 'foo', []);
        $this->assertInstanceOf(Required::class, $factory->make());
    }

    /** @test */
    public function it_can_get_a_rule_instance_with_proper_arguments()
    {
        $factory = new RuleFactory('in:1,2,3', 'foo', []);
        $in = $factory->make();
        $this->assertInstanceOf(In::class, $in);
        $this->assertEquals([1, 2, 3], $in->getArguments());
    }

    /** @test */
    public function it_can_check_against_required_rule()
    {
        $this->validationFailureCheck(
            $rule = ['name' => 'required'],
            'The name is required',
            [],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 'abcd'],
            'name',
        );
    }

    /** @test */
    public function it_can_check_against_min_rule()
    {
        $this->validationFailureCheck(
            $rule = [
                'name' => 'required|min:4'
            ],
            $message = 'The name must at least be 4 long',
            ['name' => 'ab'],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 'abcd'],
            'name',
        );
    }

    /** @test */
    public function it_can_check_against_max_rule()
    {
        $this->validationFailureCheck(
            $rule = [
                'name' => 'required|max:4'
            ],
            $message = 'The name must not exceed 4 long',
            ['name' => 'abcde'],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 'abc'],
            'name',
        );

    }

    /** @test */
    public function it_can_check_against_in_rule()
    {
        $this->validationFailureCheck(
            $rule = [
                'name' => 'required|in:1,2,3'
            ],
            $message = 'The name is not in 1,2,3',
            ['name' => 'abcde'],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 3],
            'name',
        );
    }

    /** @test */
    public function it_can_check_against_starts_with_rule()
    {
        $this->validationFailureCheck(
            $rule = [
                'name' => 'required|starts_with:z'
            ],
            'The name must starts with z',
            ['name' => 'abcde'],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 'zah'],
            'name',
        );

    }

    /** @test */
    public function it_can_check_against_ends_with_rule()
    {
        $message = 'The name must ends with z';
        $this->validationFailureCheck(
            $rule = [
                'name' => 'required|ends_with:z'
            ],
            $message,
            ['name' => 'abcde'],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 'haz'],
            'name',
        );
    }

    /** @test */
    public function it_can_check_against_email_rule()
    {
        $message = 'The email must be a valid email';
        $this->validationFailureCheck(
            $rule = [
                'email' => 'required|email'
            ],
            $message,
            ['email' => 'not a email'],
            'email',
        );

        $this->validationSuccessCheck(
            $rule,
            ['email' => 'email@email.com'],
            'email',
        );
    }

    /** @test */
    public function it_can_check_against_between_rule()
    {
        $message = 'The name must between 3,30';
        $this->validationFailureCheck(
            $rule = ['name' => 'required|between:3,30'],
            $message,
            ['name' => 31],
            'name',
        );

        $this->validationSuccessCheck(
            $rule,
            ['name' => 30],
            'name',
        );
    }

    /** @test */
    public function it_can_check_against_required_if_rule()
    {
        $validator = new Validator(['age' => 31]);
        try {
            $validator->validate([
                'name' => 'required_if:age'
            ]);
        } catch (ValidationException $exception) {
            $message = 'The name is required';
            $this->assertErrorExist($exception, $message, 'name');
        }

        $validator = new Validator(['age' => 30, 'name' => 'david']);
        $validated = $validator->validate([
            'name' => 'required_if:age'
        ]);
        $this->assertEquals('david', $validated['name']);
        $this->assertArrayNotHasKey('age', $validated);
    }

    /** @test */
    public function it_can_check_against_a_custom_rule_object()
    {
        $rule = [
            'name' => ['required', new Custom('love')]
        ];
        $field = 'name';
        $message = 'The value is not love';
        $this->validationFailureCheck($rule, $message, ['name' => 'david'], $field);
        $this->validationSuccessCheck($rule, ['name' => 'love'], $field);
    }

    /** @test */
    public function it_support_custom_validation_message_of_a_field()
    {
        $rule = [
            'name' => 'required'
        ];
        $field = 'name';
        $message = 'The name field is missing, please check again';
        $this->validationFailureCheck($rule, $message, [], $field, ['name.required' => $message]);
    }

    /** @test */
    public function it_support_custom_validation_message_of_a_rule()
    {
        $rule = [
            'name' => 'required|in:4,5,6,7'
        ];
        $field = 'name';
        $message = 'The :key must in :argument, you had provided :value, please submit again';
        $errorMessage = 'The name must in 4,5,6,7, you had provided 1, please submit again';
        $this->validationFailureCheck($rule, $errorMessage, ['name' => 1], $field, ['in' => $message]);

        $errorMessage = 'The name must in 4,5,6,7, you had provided 2, please submit again';
        $this->validationFailureCheck($rule, $errorMessage, ['name' => 2], $field, ['in' => $message]);
    }

    /** @test */
    public function it_support_closure_validation()
    {
        $rule = [
            'name' => ['required',
                fn($value, $key, $data) => $value === 'closure'
            ]
        ];
        $field = 'name';
        $message = 'the name is not closure, this is a custom closure error message';
        $this->validationFailureCheck($rule, $message, [], $field, ['name.closure' => $message]);
    }

    public function assertErrorExist($exception, $message, $fields)
    {
        $errors = $exception->errors();
        $this->assertTrue(in_array($message, $errors['errors'][$fields]));
    }

    private function validationFailureCheck($rule, string $message, $data, $field, $validationMessage = [])
    {
        $validator = new Validator($data);
        try {
            $validator->validate($rule, $validationMessage);
        } catch (ValidationException $exception) {
            $this->assertErrorExist($exception, $message, $field);
        }
    }

    private function validationSuccessCheck(array $rules, array $data, $field)
    {
        $validator = new Validator($data);
        $validated = $validator->validate($rules);
        $this->assertEquals($data[$field], $validated[$field]);
        return $validated;
    }
}
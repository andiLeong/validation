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
        $rule = ['name' => 'required'];
        $message = 'The name is required';
        $this->validationFailureCheck($rule, $message, [], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ' '], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'abcd'], 'name');
        $this->validationSuccessCheck($rule, ['name' => 0], 'name');
        $this->validationSuccessCheck($rule, ['name' => '0'], 'name');
        $this->validationSuccessCheck($rule, ['name' => false], 'name');
        $this->validationSuccessCheck($rule, ['name' => true], 'name');
    }

    /** @test */
    public function it_can_check_against_nullable_rule()
    {
        $rule = ['foo' => 'nullable'];
        $this->validationSuccessCheck($rule, ['foo' => ''], 'foo');
        $this->validationSuccessCheck($rule, ['foo' => null], 'foo');

        $validator = new Validator([]);
        $data = $validator->validate($rule);
        $this->assertArrayNotHasKey('foo', $data);
    }

    /** @test */
    public function it_can_check_against_min_rule()
    {
        $rule = ['name' => 'min:4'];
        $message = 'The name must at least be 4 long';
        $this->validationFailureCheck($rule, $message, ['name' => 'ab'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => true], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ' '], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'abcd'], 'name');
        $this->validationSuccessCheck($rule, ['name' => '0000'], 'name');
        $this->validationSuccessCheck($rule, ['name' => [3,5,6,7,7]], 'name');
    }

    /** @test */
    public function it_can_check_against_json_rule()
    {
        $rule = ['name' => 'json'];
        $message = 'The name must be valid JSON';
        $this->validationFailureCheck($rule, $message, ['name' => 'ab'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '"f":"x'], 'name');

        $this->validationSuccessCheck($rule, ['name' => '{"foo":"bar"}'], 'name');
    }

    /** @test */
    public function it_can_check_against_ip_rule()
    {
        $rule = ['name' => 'ip'];
        $message = 'The name must be valid ip address';
        $this->validationFailureCheck($rule, $message, ['name' => 'ab'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');

        $this->validationSuccessCheck($rule, ['name' => '127.0.0.1'], 'name');
        $this->validationSuccessCheck($rule, ['name' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], 'name');
        $this->validationSuccessCheck($rule, ['name' => '192.0.2.146'], 'name');
    }

    /** @test */
    public function it_can_check_against_is_string_rule()
    {
        $rule = ['name' => 'is_string'];
        $message = 'The name must be valid string';
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');

        $this->validationSuccessCheck($rule, ['name' => '127.0.0.1'], 'name');
        $this->validationSuccessCheck($rule, ['name' => 'foo'], 'name');
    }

    /** @test */
    public function it_can_check_against_alpha_num_rule()
    {
        $rule = ['name' => 'alpha_num'];
        $message = 'The name must be alpha and number';
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '127.0.0.1'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ' '], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '-sas'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '_1'], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'foo'], 'name');
        $this->validationSuccessCheck($rule, ['name' => 'foo32'], 'name');
        $this->validationSuccessCheck($rule, ['name' => 'FFO'], 'name');
    }

    /** @test */
    public function it_can_check_against_is_array_rule()
    {
        $rule = ['name' => 'is_array'];
        $message = 'The name must be valid array';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 990], 'name');

        $this->validationSuccessCheck($rule, ['name' => []], 'name');
    }

    /** @test */
    public function it_can_check_against_is_boolean_rule()
    {
        $rule = ['name' => 'boolean'];
        $message = 'The name must be boolean';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 990], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');

        $this->validationSuccessCheck($rule, ['name' => true], 'name');
        $this->validationSuccessCheck($rule, ['name' => false], 'name');
        $this->validationSuccessCheck($rule, ['name' => 1], 'name');
        $this->validationSuccessCheck($rule, ['name' => 0], 'name');
        $this->validationSuccessCheck($rule, ['name' => '0'], 'name');
        $this->validationSuccessCheck($rule, ['name' => '1'], 'name');
    }

    /** @test */
    public function it_can_check_against_is_timestamp_rule()
    {
        $rule = ['name' => 'timestamp'];
        $message = 'The name must be valid timestamp';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => true], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -300], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 0], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 3.98], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '300'], 'name');

        $this->validationSuccessCheck($rule, ['name' => 1], 'name');
        $this->validationSuccessCheck($rule, ['name' => 600], 'name');
    }

    /** @test */
    public function it_can_check_against_decimals_rule()
    {
        $rule = ['name' => 'decimal'];
        $message = 'The name must be valid floating decimal';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 990], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -990], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -990.98], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '99.9'], 'name');

        $this->validationSuccessCheck($rule, ['name' => 1.5], 'name');

        $rule = ['name' => 'decimal:3'];
        $this->validationFailureCheck($rule, $message, ['name' => 1.2], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 1.2222], 'name');

        $this->validationSuccessCheck($rule, ['name' => 1.567], 'name');

        $rule = ['name' => 'decimal:1,3'];
        $this->validationFailureCheck($rule, $message, ['name' => 1.2222], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 1.22224], 'name');

        $this->validationSuccessCheck($rule, ['name' => 1.567], 'name');
        $this->validationSuccessCheck($rule, ['name' => 1.5], 'name');
        $this->validationSuccessCheck($rule, ['name' => 1.56], 'name');
    }

    /** @test */
    public function it_can_check_against_timezone_rule()
    {
        $rule = ['name' => 'timezone'];
        $message = 'The name must be valid php timezone';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 990], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'Asia/Aden'], 'name');
    }

    /** @test */
    public function it_can_check_against_number_rule()
    {
        $rule = ['name' => 'number'];
        $message = 'The name must be a number';
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 'foo'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 990.43], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => 0.43], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '998'], 'name');

        $this->validationSuccessCheck($rule, ['name' => 998], 'name');
        $this->validationSuccessCheck($rule, ['name' => 0], 'name');
        $this->validationSuccessCheck($rule, ['name' => -1], 'name');
    }

    /** @test */
    public function it_can_check_against_positive_number_rule()
    {
        $rule = ['name' => 'number:positive'];
        $message = 'The name must be a positive number';
        $this->validationFailureCheck($rule, $message, ['name' => 990.43], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -43], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -3], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -0], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -1], 'name');

        $this->validationSuccessCheck($rule, ['name' => 998], 'name');
        $this->validationSuccessCheck($rule, ['name' => 0], 'name');
        $this->validationSuccessCheck($rule, ['name' => 1], 'name');
    }

    /** @test */
    public function it_can_check_against_max_rule()
    {
        $rule = ['name' => 'max:4'];
        $message = 'The name must not exceed 4 long';
        $this->validationFailureCheck($rule, $message, ['name' => 'abcde'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => [2, 3, 4, 5, 6]], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'abc'], 'name');
        $this->validationSuccessCheck($rule, ['name' => ' '], 'name');
        $this->validationSuccessCheck($rule, ['name' => ''], 'name');
        $this->validationSuccessCheck($rule, ['name' => []], 'name');

    }

    /** @test */
    public function it_can_check_against_in_rule()
    {
        $rule = ['name' => 'required|in:1,2,3'];
        $message = 'The name is not in 1,2,3';

        $this->validationFailureCheck($rule, $message, ['name' => 'abcde'], 'name');

        $this->validationSuccessCheck($rule, ['name' => 3], 'name');
        $this->validationSuccessCheck($rule, ['name' => '1'], 'name');
    }

    /** @test */
    public function it_can_check_against_starts_with_rule()
    {
        $rule = ['name' => 'starts_with:z'];
        $message = 'The name must starts with z';
        $this->validationFailureCheck($rule, $message, ['name' => 'abcde'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ' '], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'zah'], 'name');
    }

    /** @test */
    public function it_can_check_against_ends_with_rule()
    {
        $rule = ['name' => 'required|ends_with:z'];
        $message = 'The name must ends with z';
        $this->validationFailureCheck($rule, $message, ['name' => 'abcde'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ' '], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'haz'], 'name');
    }

    /** @test */
    public function it_can_check_against_email_rule()
    {
        $message = 'The email must be a valid email';
        $rule = ['email' => 'email'];
        $this->validationFailureCheck($rule, $message, ['email' => 'not a email'], 'email');
        $this->validationFailureCheck($rule, $message, ['email' => []], 'email');

        $this->validationSuccessCheck($rule, ['email' => 'email@email.com'], 'email');
    }

    /** @test */
    public function it_can_check_against_between_rule()
    {
        $message = 'The name must between 3,30';
        $rule = ['name' => 'between:3,30'];
        $this->validationFailureCheck($rule, $message, ['name' => 31], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => '31'], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => -2], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => []], 'name');
        $this->validationFailureCheck($rule, $message, ['name' => false], 'name');

        $this->validationSuccessCheck($rule, ['name' => 30], 'name');
        $this->validationSuccessCheck($rule, ['name' => 3], 'name');
    }

    /** @test */
    public function it_can_check_against_required_if_rule()
    {
        $message = 'The name is required';
        $rule = ['name' => 'required_if:age'];

        $this->validationFailureCheck($rule, $message, ['age' => 31], 'name');
        $this->validationFailureCheck($rule, $message, ['age' => 31, 'name' => ''], 'name');
        $this->validationFailureCheck($rule, $message, ['age' => 31, 'name' => null], 'name');
        $this->validationFailureCheck($rule, $message, ['age' => 31, 'name' => ' '], 'name');
        $this->validationFailureCheck($rule, $message, ['age' => 31, 'name' => []], 'name');

        $this->validationSuccessCheck($rule, ['name' => 'david'], 'name');
        $validated = $this->validationSuccessCheck($rule, ['age' => 30, 'name' => 'david'], 'name');
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

    /** @test */
    public function all_validation_errors_should_return()
    {
        $rule = ['name' => 'required', 'age' => 'required'];
        $this->validationFailureCheck(
            $rule,
            'The name is required',
            [],
            'name',
        );

        $this->validationFailureCheck(
            $rule,
            'The age is required',
            [],
            'age',
        );

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
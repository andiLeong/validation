# Validation

Includes some handful validation rules, it supports custom error message, closure syntax rule , passing object rule , no framework required.

but only some validation rules are include like
##### required
##### required_if
#### min
#### max
#### between
#### in


Interested on how the Laravel validation works that's main reason drive me develop this package. Not intent to use in production but just for quick exercise

When package is complete. I compare this to Laravel's validation component, it really helps me to have better understanding of
how the framework validator works under the hood and really improve my skill-sets.

#### Reading code is as important as writing code.

# How to use

```

$data = [
    'name' => 'foo',
];

$validator = new Validator($data);  
try{  
    $validated = $validator->validate([  
        'name' => 'required|min:3|max:5',  
        'age' => ['required','between:18,60'],  
        'foo' => ['required',fn($value) => $value == 'bar'],  
        'object' => ['required',new Custom('validation')],  
        'sex' => 'required_if:name',  
    ],[  
        'name.max' => 'The :key must in :argument long, you had provided :value which is not valid',  
        'foo.closure' => 'The :key must in bar'  
    ]);
  
}catch(ValidationException $exception){  
  
    //validation fails do whatever
    //$errors contains all errors message.
    $errors = $exception->errors();
  
}  

$validated is the return array only contains the rules keys
  
```
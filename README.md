# Validation library

A framework independent validation library that focuses on expressiveness and flexibility.

## Installation

You can download the code from [Github](https://github.com/fuelphp/validation) or install it through packagist
by including `fuel/validation` in your project composer.json require:

    "require": {
        "fuel/validation":  "dev-master"
    }

## Usage

Let's start with an example to get a bit of a feel.

```php
<?php

$val = new Fuel\Validation\Base();

$val->validate('username', function($v) {
	return $v->require()
		and $v->atLeastChars(4)
		and $v->atMostChars(25);
});

$input = array('username' => 'Something');

$success = $val->execute($input);
```

### Step by step

* Create an instance of Validation
* Start adding *validators* by calling `->validate($field, $validator)` on the Validation object. Validators
are Closures that take just one arguement: a Validation\Value object (extending Validation\Value\Validatable)
* Inside the validator you define your rules in the expressive manner exemplified above, ik almost reads
naturally what it means: *require the value that must be at least 4 characters and at most 25*.
* Once you've defined your validators you execute() the validation with the input you want it to validate.
This can be manual input like above, a modified model instance or a superglobal like `$_POST`.
* Once done the execution returns the success as a boolean.
* You can fetch a list of errors by calling `$val->getError()` or a list of validated values by calling
`$val->getValue()`. Both can be called with a $key param to fetch a specific value/error.

**Note:** when you pass objects they may be edited directly during validation.

### More to come...
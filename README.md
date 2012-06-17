# Validation library

A framework independent validation library that focuses on expressiveness and flexibility.

## Installation

It is best installed it through packagist by including `fuel/validation` in your project composer.json require:

    "require": {
        "fuel/validation":  "dev-master"
    }

You can also download it from Github, but no autoloader is provided so you'll need to register it with your own 
PSR-0 compatible autoloader.

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
// bit more complex, screen_name validation dependent upon username:
$val->validate('screen_name', function($v) {
	if ($v->get() and $v->get() === $v->getValidation()->getValue('username'))
	{
		$v->setError('Screen name must not match the chosen username.');
		return false;
	}

	return $v->require() and $v->atLeastChars(4);
});

$input = array('username' => 'Something', 'screen_name' => 'Another');

$success = $val->execute($input);
```

### Step by step

* Create an instance of Validation
* Start adding *validators* by calling `->validate($key, $validator)` on the Validation object. The $key is
the name of the property or array key from the Input you're validating. Validators are Closures that take
just one arguement: a Validation\Value object (extending Validation\Value\Validatable)
* Inside the validator you define your rules in the expressive manner exemplified above, ik almost reads
naturally what it means: *require the value that must be at least 4 characters and at most 25*.
* Once you've defined your validators you execute() the validation with the input you want it to validate.
This can be manual input like above, a modified model instance or a superglobal like `$_POST`.
* Once done the execution returns the success as a boolean.
* You can fetch a list of errors by calling `$val->getError()` or a list of validated values by calling
`$val->getValue()`. Both can be called with a $key param to fetch a specific value/error.

**Note:** when you pass objects they may be edited directly during validation.  
**Note 2:** to access deeper array values you can use dot.natation: `'groups.admin.name'` would access
`'$input['group']['admin']['name']` for example. This works both on arrays and objects.

### Wildcards for validating deeper array values

If you submit multiple users for example you don't want to have to repeat the validation rules definition
for each user submitted. For this you can use the asterisk (*) as a wildcard. In the example below it's
the expectation you named your fields like `users[0][username]` and `users[0][password]` in the form.

```php
<?php
$v->validate('users.*.username', function(Value\Valuable $v) {
    return $v->require() and $v->atLeastChars(4);
})->validate('users.*.password', function (Value\Valuable $v) {
    return $v->require() and $v->atLeastChars(4);
});
```

These will validate the username and password for each entry in the 'users' array of the input.

### Validation methods

#### Included

All these methods are part of the `Fuel\Validation\RuleSet\Base` class. Except for `require()` all will
validate successfully on empty input, if empty is not valid input the first call should be to `require()`.
Each also returns `true` or `false` but that is only to allow usage like above (with `and` and `or`).

Eache of these checks whether the value being validated...

* __require()__  
...is non-empty, contrary to PHP zero (`0` and `'0'`) is considered non-empty.
* __matchesValue(string $value, bool $strict = false)__  
...matches a specific value.
* __matchesInput(string $key, bool $strict = false)__  
...matches another input with $key in the current validation object.
* __matchesPattern(string $pattern)__  
$pattern must be a valid full `preg_match()` pattern.
* __inArray(array $array, $strict = false)__  
...is in the given array.
* __atLeastChars($length)__  
...is at least $length characters long.
* __atMostChars($length)__  
...is at most $length characters long.
* __exactChars($length)__  
...is exactly $length characters long.
* __atLeastNum($number)__  
...is numericly at least $number.
* __atMostNum($number)__  
...is numericly at most $number.
* __isEmail()__  
...validates as a valid emailaddress according to PHP's filter_var().
* __isUrl()__  
...validates as a valid URL according to PHP's filter_var().
* __isIp()__  
...validates as a valid IP address according to PHP's filter_var().

#### Adding your own RuleSets

You can add more methods by creating a class with methods prefixed by `validate` and the next character
being uppercase. Below is require as an example of how a method should be defined (note the first param
being `Fuel\Validation\Value\Valuable` typehinted) and how it should fail:

```php
<?php
public function validateRequire(\Fuel\Validation\Value\Valuable $v)
{
    $var = $v->get();
    if ($var === null or $var === '' or $var === array())
    {
        $v->setError('nonEmpty');
        return false;
    }

    return true;
}
```

You can add such a class of your own by calling `addRuleSet($ruleSet)` on the Validation object. The
`$ruleSet` may be either a string containing a full classname (including the namespace) or an
instantiated object.

#### PHP internal & user-defined functions

You can also call functions upon the objects. First all the RuleSets will be checked for the function's
name prefixed with 'validate', if that fails it will do a last attempt to global for a function with the
rule's name.

As especially PHP function's won't be able to deal with the Validation\Value object the output of
functions is handled differently. Instead just the actual value will be passed as the first argument. For
the output there's two possibilities:

* Boolean: true is handled as successful validation, false means it failed and an error is given
with the rule as the message value. (example: `$v->is_numeric()`)
* Everything else: the value is changed to whatever will be the output by the function. (example:
`$v->trim()`)

## The Validation object

### Methods

* __validate(string $key, Closure $validator):Fuel\Validation\Base__  
Add a new validator for $key.
* __execute(array|object $input):bool__  
Run the validators on $input.
* __getValue(string $key, mixed $default = null):mixed__  
(after/during execution) Fetch a value from the input.
* __setValue(string $key, mixed $value):Fuel\Validation\Base__  
(after/during execution) Change a value on the input.
* __getValidated(string $key, mixed $default = null):string|array__  
(after/during execution) Fetch a value that already validated successfully.
* __getError(string $key):Fuel\Validation\Error\Base|array__  
(after/during execution) Fetch a specific error object or all in an array.
* __getErrorMessage(string $key):string|array__  
(after/during execution) Fetch just a specific error message or all in an array.
* __setMessage(string $error, string $message):Fuel\Validation\Base__  
Set a message for the given $error key.
* __getMessage(string $error, mixed $default):string__  
Get a message for the given $error key.
* __executeRule(string $rule, \Fuel\Validation\Value\Valuable $value, array $args):mixed__  
Execute a rule within the validaion object on the given $value with additional $args.

## The Validation\Value object

For each validator you create you get passed an instance of `Fuel\Validation\Value\Valuable` which
represents the value being validated. As you've seen you can call the rules as methods upon this object
but there's more you can do with it. You could completely forgo any rules and just do the validating
within the validator Closure. The most important methods are listed below.

### Methods

* __get():mixed__  
Returns the current value that you are validating
* __set(mixed $value):Fuel\Validation\Value\Valuable__  
Changes the current value being validated
* __getKey():string__  
Returns the key for the value you are validating
* __setKey(string $key):Fuel\Validation\Value\Valuable__  
Changes the key
* __getError():string__  
Returns any error string already set
* __setError(string $error):Fuel\Validation\Value\Valuable__  
Set an error message/language key, once set the field is considered to have failed validation
* __resetError():Fuel\Validation\Value\Valuable__  
Reset the error for this value to `null`, meaning it'll pass
* __validates():bool__  
Has the value validated up till now?
* __getValidation():Fuel\Validation\Base__  
Returns the parent Validation object to which this value belongs

# More to come...
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

### Validation methods

#### Included

All these methods are part of the `Fuel\Validation\RuleSet\Base` class. Except for `require()` all will
validate successfully on empty input, if empty is not valid input the first call should be to `require()`.

* __require()__
* __matchesValue(string $value, bool $strict = false)__
* __matchesInput(string $key, bool $strict = false)__ - Looks in the validation object's input for the value
$key and matches it to the value of the current Value object.
* __matchesPattern(string $pattern)__ - $pattern must be a valid full `preg_match()` pattern.
* __inArray(array $array, $strict = false)__
* __atLeastChars($length)__
* __atMostChars($length)__
* __exactChars($length)__
* __atLeastNum($number)__
* __atMostNum($number)__
* __isEmail()__ - uses PHP's filter_var()
* __isUrl()__ - uses PHP's filter_var()
* __isIp()__ - uses PHP's filter_var()

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

* __validate(string $key, Closure $validator)__
* __execute(array|object $input)__
* __getValue(string $key, mixed $default = null)__
* __setValue(string $key, mixed $value)__
* __getValidated(string $key, mixed $default = null)__
* __getError(string $key)__
* __executeRule(string $rule, \Fuel\Validation\Value\Valuable $value, array $args)__

## The Validation\Value object

For each validator you create you get passed an instance of `Fuel\Validation\Value\Valuable` which
represents the value being validated. As you've seen you can call the rules as methods upon this object
but there's more you can do with it. You could completely forgo any rules and just do the validating
within the validator Closure. The most important methods are listed below.

### Methods

* __get()__ - returns the current value that you are validating
* __set(mixed $value)__ - changes the current value being validated
* __getKey()__ - returns the key for the value you are validating
* __setKey(string $key)__ - changes the key
* __getError()__ - returns any error string already set
* __setError(string $error)__ - set an error message/language key, once set the field is considered to
have failed validation
* __resetError()__ - reset the error for this value to `null`, meaning it'll pass
* __validates()__ - has the value validated up till now?
* __getValidation()__ - returns the parent Validation object to which this value belongs

# More to come...
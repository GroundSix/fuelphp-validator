# Validation library

A framework independent validation library.

## Example

As there's no documentation yet, here's a code example to show how this should end up working.

```php
<?php

use Fuel\Validation;
use Fuel\Validation\Value\Valuable as Value;

$val = new Validation\Base();

$val->validate('username', function(Value $v) {
	return $v->nonEmpty()
		and $v->isNumeric()
		and $v->someCustomRule();
});

$input = array('username' => 'Something');

$success = $val->execute($input);
```
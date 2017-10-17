---
layout: document
title: "Between (float)"
date: 2017-03-15 17:28:56
categories: Validators
---

## Between (float) Validator

The `BetweenFloatValidator` validates that the string sent to the validator is between
the defined floating point numbers.

```php?start_inline=1
$validate = BetweenFloatValidator::create(1.5, 3.5);
$validate->check('A dirty martini.');   //false
$validate->check(1.6);                  //true
$validate->check('1.87');               //true
$validate->check(1.34);                 //false
$validate->check('3.784');              //false
```

### Constructor Arguments

In addition to the values for minimum and maximum the constructor for the `BetweenFloatValidator` 
object also accepts an argument to set the error message that you would like returned 
from the object.

```php?start_inline=1
$validate = BetweenFloatValidator::create(1, 10, 'Please enter a number between 1 and 10.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('age')
    ->addValidator(BetweenValidator::create(21, 120, 'Enter your age.'));
```
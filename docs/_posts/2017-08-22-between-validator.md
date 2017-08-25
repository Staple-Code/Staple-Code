---
layout: document
title: "Between (integer)"
date: 2017-03-15 17:28:56
categories: Validators
---

## Between Validator

The `BetweenValidator` validates that the string sent to the validator is between
the defined integers.

```php?start_inline=1
$validate = BetweenValidator::create(1, 10);
$validate->check('A dirty martini.');   //false
$validate->check(5);                    //true
$validate->check('5');                  //true
$validate->check(10);                   //true
$validate->check(1.6);                  //false
$validate->check('1.87');               //false
```

### Constructor Arguments

In addition to the values for minimum and maximum the constructor for the `BetweenFloatValidator` 
object also accepts an argument to set the error message that you would like returned 
from the object.

```php?start_inline=1
$validate = BetweenValidator::create(1, 10, 'Please enter a number between 1 and 10.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('age')
    ->addValidator(BetweenFloatValidator::create(21, 120, 'Enter your age.'));
```
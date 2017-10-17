---
layout: document
title: "Alpha Numeric"
date: 2017-03-15 17:28:56
categories: Validators
---

## Alpha Numeric Validator

The `AlnumValidator` validates that the string sent to the validator is a composition 
of digit and alphabetical characters. This method uses the `ctype_alnum()` function 
for validation of the supplied values.

```php?start_inline=1
$validate = AlnumValidator::create();
$validate->check('A dirty martini.');   //false - space is not alphanumeric
$validate->check('racecar');            //true
$validate->check('1234');               //true
$validate->check(1.34);                 //false
$validate->check('3.14159');            //false
```

### Constructor Arguments

The constructor for the `NumericValidator` object also accepts an argument to set the
error message that you would like returned from the object.

```php?start_inline=1
$validate = AlnumValidator::create('Enter a valid username.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('username')
    ->addValidator(AlnumValidator::create());
```
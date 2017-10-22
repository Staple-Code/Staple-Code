---
layout: document
title: "Numeric"
date: 2017-03-15 17:28:56
categories: Validators
---

## Numeric Validator

The `NumericValidator` validates that the string sent to the validator is an integer value.

```php?start_inline=1
$validate = NumericValidator::create();
$validate->check('A dirty martini.');   //false
$validate->check(1234);                 //true
$validate->check('1234');               //true
$validate->check(1.34);                 //false
$validate->check('3.14159');            //false
```

### Constructor Arguments

The constructor for the `NumericValidator` object also accepts an argument to set the
error message that you would like returned from the object.

```php?start_inline=1
$validate = NumericValidator::create('Please enter a valid age.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('age')
    ->addValidator(NumericValidator::create());
```
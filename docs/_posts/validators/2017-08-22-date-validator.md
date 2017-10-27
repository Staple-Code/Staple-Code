---
layout: document
title: "Date"
date: 2017-03-15 17:28:56
categories: Validators
---

## Date Validator

The `DateValidator` validates that the string sent to the validator is a valid date.
Validation is based a regex string.

```php?start_inline=1
$validate = DateValidator::create();
$validate->check('2017-10-10');         //true
$validate->check('10/10/1970');         //true
$validate->check('Today');              //false
$validate->check('now');                //false
$validate->check('June 7th, 2008');     //false
```

### Constructor Arguments

The `DateValidator` object also accepts an argument to set the error message that you
would like returned from the object.

```php?start_inline=1
$validate = DateValidator::create('Please enter a valid date.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('BirthDate')
    ->addValidator(DateValidator::create('Enter your birth date.'));
```
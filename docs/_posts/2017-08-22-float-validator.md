---
layout: document
title: "Float"
date: 2017-03-15 17:28:56
categories: Validators
---

## Float Validator

The `FloatValidator` validates that the string sent to the validator is an floating
point value.

```php?start_inline=1
$validate = FloatValidator::create();
$validate->check('A dirty martini.');   //false
$validate->check(1234);                 //true
$validate->check('1234');               //true
$validate->check(1.34);                 //true
$validate->check('3.14159');            //true
```

### Constructor Arguments

The constructor for the `FloatValidator` object also accepts an argument to set the
error message that you would like returned from the object.

```php?start_inline=1
$validate = FloatValidator::create('Please enter a valid age.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('age')
    ->addValidator(FloatValidator::create());
```
---
layout: document
title: "Email"
date: 2017-03-15 17:28:56
categories: Validators
---

## Email Validator

The `EmailValidator` validates that the string sent to the validator is a email address.
Validation is based a regex string.

```php?start_inline=1
$validate = EmailValidator::create();
$validate->check('test@test.com');          //true
$validate->check('my@email');               //false
$validate->check('something@info.co.uk');   //true
$validate->check('copy@mysite.io');         //true
$validate->check('myemail.com');            //false
```

### Constructor Arguments

The `EmailValidator` object also accepts an argument to set the error message that you
would like returned from the object.

```php?start_inline=1
$validate = EmailValidator::create('Enter a valid email address.');
```

### Usage with Forms

Often times you use this validator with form fields.

```php?start_inline=1
$field = TextElement::create('email')
    ->addValidator(EmailValidator::create('Enter your email address.'));
```
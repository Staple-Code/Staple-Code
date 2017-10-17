---
layout: document
title: "Validating Forms"
date: 2017-03-15 17:28:56
categories: Forms
---

## Validating Forms

Staple includes built in objects to assist validating form data. These Field Validators
can be created on each input field to ensure the data that the user enters matches
the values that you expect.

### Field Validators

The `FieldValidator` abstract class allows the creation of validators to verify form
data that has been entered. Each validator has it's own rules for validation and you
can create your own validators as long as they are based on this base class.

For more information on specific validators see the **Validators** section below.

### Usage with Forms

Validators work on form elements to validate the data entered into the form fields.
You define your validators and attach them to work fields. You can enter multiple
validators on each field. 

**Note:** Each validator should be unique to each form field of the error checking 
could get overwritten by subsequent fields.

```php?start_inline=1
$form = new Form();

TextElement::create('first_name', 'First Name')
    ->addValidator(LengthValidator::create(2,50))
    ->addToForm($form);
    
TextElement::create('last_name', 'Last Name')
    ->addValidator(LengthValidator::create(2,50))
    ->addToForm($form);

TextElement::create('age', 'Age')
    ->addValidator(NumericValidator::create())
    ->addToForm($form);
    
TextElement::create('username', 'Username')
    ->addValidator(AlnumValidator::create())
    ->addValidator(LengthValidator::create(8,20))
    ->addToForm($form);
```

### Validation Callback

You can assign a callback method to validate more complex form conditions. The
validation callback method must return a boolean signifying whether the custom conditions
were met or not.

```php?start_inline=1
$form->addValidationCallback(function(){
    //Do Something
});
```

### Is the Form Valid?

The validators attached to each field are checked when the `validate()` method is 
called on the form object.

```php?start_inline=1
$valid = $form->validate();
```

### Extracting Errors

```php?start_inline=1
$errors = $form->getErrors();
```
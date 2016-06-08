# Laravel Validation

[![StyleCI](https://styleci.io/repos/59241241/shield?style=flat)](https://styleci.io/repos/59241241)
[![Build Status](https://travis-ci.org/samrap/laravel-validation.svg?branch=master)](https://travis-ci.org/samrap/laravel-validation)
[![Latest Stable Version](https://poser.pugx.org/samrap/laravel-validation/v/stable)](https://packagist.org/packages/samrap/laravel-validation)
[![Total Downloads](https://poser.pugx.org/samrap/laravel-validation/downloads)](https://packagist.org/packages/samrap/laravel-validation)
[![Latest Unstable Version](https://poser.pugx.org/samrap/laravel-validation/v/unstable)](https://packagist.org/packages/samrap/laravel-validation)
[![License](https://poser.pugx.org/samrap/laravel-validation/license)](https://packagist.org/packages/samrap/laravel-validation)

### Laravel Validation
---
Laravel Validation is a bare-bones, minimal validation package for the Laravel framework. Its only purpose is to provide a way to separate your request validation rules from your models and controllers, neither of which should contain such information. It accomplishes this by essentially acting as a broker between your validation rules and a Laravel validator instance.

```
public function store(Request $request, ModelValidator $validator)
{
    $validator = $validator->validate($request->all());
    $this->validateWith($validator, $request);

    ...
}
```

### Installation
---
Install via Composer:

`composer require samrap/laravel-validation`

Then add the service provider to your providers array in `config/app.php`:

`Samrap\Validation\ValidationServiceProvider::class`

Finally, the base `Validator` class needs to be published to a new `app/Validation` directory. This can be done using the `vendor:publish` command:

`php artisan vendor:publish`

### Usage
---
All validators reside in the `app/Validation` directory and extend the abstract class `App\Validation\Validator`. There should be one validator class per model. For example, the validator for a `User` model could be called `UserValidator`.

Laravel Validation provides a useful artisan command for generating new validators on the fly. Let's create a validator for our `User` model and define some rules:

`php artisan make:validator UserValidator`

This will create a new `UserValidator` class in the `app/Validation` directory that looks like this:

```
<?php

namespace App\Validation;

use App\Validation\Validator;

class UserValidator extends Validator
{
    /**
     * The validation rules.
     *
     * @var array
     */
    protected $rules = [];
}
```

Each validator has a `rules` property which (suitably) houses all the validation rules for the intended model. Let's define some basic rules for this validator:

```
/**
 * The validation rules.
 *
 * @var array
 */
protected $rules = [
    'email' => 'email|required|max:255|unique:user',
    'password' => 'min:8|confirmed',
];
```

Great! We now have a validator class named `UserValidator` with the rules we intend to validate with in our controller. Let's jump over to the `UserController` and see how to use this new validator class.

First, we will want to import this class into our controller:

`use App\Validation\UserValidator`

Now, let's validate a POST request for the controller's `store` method:

```
public function store(Request $request, UserValidator $validator)
{
    $validator = $validator->validate($request->all());
    $this->validateWith($validator, $request);

    ...
}
```

A few things are going on here. Let's go line by line.

First, in addition to the current request, we are type hinting an instance of our `UserValidator` as it has dependencies that should be resolved via the service container:

`public function store(Request $request, UserValidator $validator)`

Our validator inherits a `validate` method from its parent class, `Samrap\Validation\Validator`, which we can use to obtain an `Illuminate\Validation\Validator` instance. Our `validate` method takes the same arguments as if we were [manually creating a validator](https://laravel.com/docs/5.2/validation#manually-creating-validators) using Laravel's `Validator::make` method (more on this later). So, we will simply pass the request input to the `$validator->validate()` method:

`$validator = $validator->validate($request->all());`

Finally, we can make use of Laravel's `ValidatesRequests` trait, included by default on all controllers. It provides us with a `validateWith` method, which expects a validator instance and the request and will handle redirection if the validation fails:

`$this->validateWith($validator, $request);`

That's it! That is all you need to do to validate your requests. The validator will use the rules defined in your `UserValidator` to validate the request, in two lines of code in your controller. Obviously, this cleans up your controllers dramatically as the amount of validation you need increases.

Of course, there may be times in a certain request when you need to add to or override some of the rules you defined in your validator. No worries, it's super easy!

```
$validator = $validator->validate($request->all(), [
    'name' => 'string|required',
]);
```

In this case, we are adding a rule for a `name` field, which will be merged with our rules defined in the `UserValidator`. By default, any rules passed explicitly to the `validate` method will override the rules defined in the validator if they exist.

### Additional Features
---
###### Dynamic Rules
Since version 1.1, it is possible to have separate rules for updating and storing models, with the required `rules` property as a fallback. Simply define a `storing` and/or `updating` property on the validator in addition to the fallback `$rules`, and Laravel Validation does the rest!

```
class UserValidator extends Validator
{
    /**
     * The validation rules.
     *
     * @var array
     */
    protected $rules = [
        'password' => 'min:8|required',
    ];

    /**
     * Validation rules when updating a model.
     *
     * @var array
     */
    protected $updating = [
        'password' => 'min:8',
    ];
}
```

Upon validating, Laravel Validation checks if the controller method's name is a RESTful `update` or `store`. If it is one of these methods and its related `updating` or `storing` property exists, those rules will be used instead.

### Contributing
---
Contributions are more than welcome! You can submit feature requests to [rapaport.sam7@gmail.com](mailto:rapaport.sam7@gmail.com), or fork the repo yourself!

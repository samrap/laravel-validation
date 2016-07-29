<?php

namespace Samrap\Validation;

use Illuminate\Validation\Factory as ValidatorFactory;

class Validator
{
    /**
     * The validator factory.
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $factory;

    /**
     * The validation errors.
     *
     * @var array
     */
    protected $errors;

    /**
     * Create a new instance of \Samrap\Validation\Validator.
     *
     * @param \Illuminate\Validation\Factory $factory
     */
    public function __construct(ValidatorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the errors from the previous validation attempt.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the validator's rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Create a new validator.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    public function validate(array $data, array $rules = [], array $messages = [], array $customAttributes = [])
    {
        // Here we allow rules to be set as a property of the child class. Any rules
        // passed to this method will take precedence over the rules property.
        $rules = array_merge($this->getRules(), $rules);

        // We persist the validation errors to this class for easier access.
        $validator = $this->factory->make($data, $rules, $messages, $customAttributes);
        $this->errors = $validator->errors();

        // We will return the validator instance and leave handling the success
        // or failure of that validation to the controller.
        return $validator;
    }
}

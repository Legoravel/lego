<?php

namespace Lego\Validation;

/**
 * Validation factory.
 */
class Validation
{
    /**
     * Get a new validation instance for the given attributes and rules.
     *
     *
     * @return \Illuminate\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return $this->getValidationFactory()->make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Validation\Factory
     */
    public function getValidationFactory(): \Illuminate\Validation\Factory
    {
        return app(\Illuminate\Contracts\Validation\Factory::class);
    }
}

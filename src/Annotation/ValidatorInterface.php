<?php

namespace MacFJA\Validator\Annotation;

/**
 * Interface ValidatorInterface.
 *
 * Define all method that a validator MUST implement.
 *
 * @author MacFJA
 * @package MacFJA\Validator\Annotation
 */
interface ValidatorInterface
{
    /**
     * Checks if the input value is valid according to the criteria of the validator
     * @return boolean <tt>true</tt> if the input is valid
     */
    public function isValid();

    /**
     * Indicate if the value can be sanitize
     * @return boolean
     */
    public function canSanitize();

    /**
     * Sanitize the input value and return the sanitized value
     * @return mixed
     * @throws \BadMethodCallException if the value can not be sanitize or if the method is not implemented
     */
    public function sanitize();

    /**
     * Returns the list of all errors according to the criteria of the validator.
     * If the input value is valid, an empty array is returned
     * @return array
     */
    public function errors();

    /**
     * Set the value to check
     * @param mixed $input The value to check
     * @return $this Chaining...
     */
    public function setInput($input);

    /**
     * Get the input value
     * @return mixed
     */
    public function getInput();

    /**
     * Do a "safe" sanitize.
     * If the value can not be sanitize (<tt>canSanitize</tt> return <tt>false</tt>) the input value is returned
     * @return mixed
     */
    public function safeSanitize();
}
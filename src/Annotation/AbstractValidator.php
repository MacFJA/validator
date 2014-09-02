<?php

namespace MacFJA\Validator\Annotation;


abstract class AbstractValidator implements ValidatorInterface
{
    private $input;

    /**
     * {@inheritdoc}
     */
    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sanitize()
    {
        throw new \BadMethodCallException('The method "sanitize" can not be called');
    }

    /**
     * {@inheritdoc}
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * {@inheritdoc}
     */
    public function safeSanitize()
    {
        return ($this->canSanitize() ? $this->sanitize() : $this->getInput());
    }

    /**
     * Create a new instance of a validator and set the input value
     * @param mixed $input The value to check
     * @return ValidatorInterface
     */
    public static function newValidator($input)
    {
        $validator = new static();
        $validator->setInput($input);
        return $validator;
    }
}
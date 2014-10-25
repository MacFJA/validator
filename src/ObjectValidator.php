<?php

namespace MacFJA\Validator;


use MacFJA\Validator\Annotation\ValidatorInterface;
use MacFJA\ValueProvider\ProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class ObjectValidator.
 * Validate an object with a list of validator.
 *
 * @author MacFJA
 * @package MacFJA\Validator
 */
class ObjectValidator
{
    /**
     * @var object
     */
    protected $object;
    /**
     * @var array List of all errors
     */
    protected $errors = array();
    /**
     * @var ProviderInterface The value provider to use
     */
    protected $valueProvider;
    /**
     * @var bool Indicate if the object have been already validate
     */
    protected $alreadyValidate = false;
    /**
     * @var array List of all validator (group by property name)
     */
    protected $validatorList = array();

    /**
     * @param mixed $object The object to validate
     * @param string $providerName FQCN of the valid provider to use
     */
    function __construct($object, $providerName = 'MacFJA\ValueProvider\GuessProvider')
    {
        $this->object = $object;
        $this->valueProvider = new $providerName();
    }

    /**
     * Add a validator
     *
     * @param string $propertyName Name of the property to validate
     * @param ValidatorInterface $validator The validator object to use
     * @return $this
     */
    public function addValidator($propertyName, $validator)
    {
        if (!isset($this->validatorList[$propertyName])) {
            $this->validatorList[$propertyName] = array();
        }
        $this->validatorList[$propertyName][] = $validator;
        return $this;
    }

    /**
     * Run each validator registered
     *
     * @return $this
     */
    protected function doAllValidation()
    {
        if ($this->alreadyValidate) {
            return $this;
        }
        $this->alreadyValidate = true;

        $propertyNames = array_keys($this->validatorList);
        foreach ($propertyNames as $property) {
            $this->doValidation($property);
        }
        return $this;
    }

    /**
     * Validate a property (run all its validators)
     *
     * @param string $property
     */
    protected function doValidation($property)
    {
        $this->errors[$property] = array();
        foreach ($this->validatorList[$property] as $validator) {
            /** @var ValidatorInterface $validator */
            $value = $this->valueProvider->getValue($this->object, $property);
            $validator->setInput($value);
            $validator->setInput($validator->safeSanitize());
            $this->errors[$property] = array_merge($validator->errors(), $this->errors[$property]);

        }
    }

    /**
     * Return the number of found error during the validation
     * @return int
     */
    public function getErrorsCount()
    {
        $this->doAllValidation();
        $count = 0;
        foreach ($this->errors as $errorGroup) {
            $count += count($errorGroup);
        }

        return $count;
    }

    /**
     * Indicate if the object is valid (<=> no error)
     * @return bool
     */
    public function isValid()
    {
        return $this->getErrorsCount() == 0;
    }

    /**
     * Return the list of errors.
     * The resulted array have this form<pre>{
     *     "property1": [
     *         "Error number 1 for property1",
     *         "Error number 2 for property1"
     *     ],
     *     "property2": [
     *         "Error of property2"
     *     ]
     *     "property3": []
     * }</pre>
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->doAllValidation()->errors;
    }

    /**
     * Sanitize (if possible) the object properties
     * @return object
     */
    public function getSanitizedObject()
    {
        $object = clone $this->object;
        $propertyNames = array_keys($this->validatorList);
        foreach ($propertyNames as $property) {
            $object = $this->sanitizeProperty($object, $property);
        }

        return $object;
    }

    /**
     * Sanitize (if possible) a property of the object (with the property's validators)
     *
     * @param mixed $object The object to sanitize
     * @param string $property The name of the property to sanitize
     * @return object
     */
    protected function sanitizeProperty($object, $property)
    {
        foreach ($this->validatorList[$property] as $validator) {
            /** @var ValidatorInterface $validator */
            $value = $this->valueProvider->getValue($object, $property);
            $validator->setInput($value);
            if ($validator->canSanitize()) {
                $this->valueProvider->setValue($object, $property, $validator->sanitize());
            }

        }

        return $object;
    }
}
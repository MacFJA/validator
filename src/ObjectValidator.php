<?php

namespace MacFJA\Validator;


use MacFJA\Validator\Annotation\ValidatorInterface;
use MacFJA\ValueProvider\ProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;

class ObjectValidator
{
    protected $object;
    protected $errors = array();
    /**
     * @var ProviderInterface
     */
    protected $valueProvider;
    protected $alreadyValidate = false;
    protected $validatorList = array();

    function __construct($object, $providerName = 'MacFJA\ValueProvider\GuessProvider')
    {
        $this->object = $object;
        $this->valueProvider = new $providerName();
    }

    public function addValidator($propertyName, $validator)
    {
        if (!isset($this->validatorList[$propertyName])) {
            $this->validatorList[$propertyName] = array();
        }
        $this->validatorList[$propertyName][] = $validator;
        return $this;
    }

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

    public function getErrorsCount()
    {
        $this->doAllValidation();
        $count = 0;
        foreach ($this->errors as $errorGroup) {
            $count += count($errorGroup);
        }

        return $count;
    }

    public function isValid()
    {
        return $this->getErrorsCount() == 0;
    }

    public function getErrors()
    {
        return $this->doAllValidation()->errors;
    }

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
     * @param mixed $object
     * @param string $property
     * @return mixed
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
<?php

namespace MacFJA\Validator;


use Doctrine\Common\Annotations\AnnotationReader;
use MacFJA\Validator\Annotation\ValidatorInterface;

class AnnotationValidator extends ObjectValidator {

    function __construct($object, $providerName = 'MacFJA\ValueProvider\GuessProvider')
    {
        parent::__construct($object, $providerName);

        $this->getAnnotationValidator();
    }

    protected function getAnnotationValidator() {
        $reader = new AnnotationReader();
        $classReflection = new \ReflectionClass($this->object);
        $propertiesReflection = $classReflection->getProperties();

        foreach ($propertiesReflection as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            $propertyName = $property->getName();
            foreach ($annotations as $annotation) {
                if ($annotation instanceof ValidatorInterface) {
                    $this->addValidator($propertyName, $annotation);
                }
            }
        }
    }
}
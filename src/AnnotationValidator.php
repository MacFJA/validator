<?php

namespace MacFJA\Validator;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use MacFJA\Validator\Annotation\ValidatorInterface;

/**
 * Class AnnotationValidator.
 * Object Validator compatible with annotation.
 *
 * @author MacFJA
 * @package MacFJA\Validator
 */
class AnnotationValidator extends ObjectValidator {
    /** @var bool Indicate if annotations have been already registered into Doctrine Annotation Registry (prevent multiple autoloader) */
    static protected $annotationsRegistered = false;

    /** {@inheritdoc} */
    function __construct($object, $providerName = 'MacFJA\ValueProvider\GuessProvider')
    {
        parent::__construct($object, $providerName);

        $this->getAnnotationValidator();
    }

    /**
     * Get all annotation and add validators
     */
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

    /**
     * Add annotations into Doctrine Annotation Registry.
     * It use <code>\class_exist</code> function with its autoload feature (that use registered autoloader).
     * Only class in the namespace "MacFJA\Validator\Annotation" are concerned by this loader
     */
    static public function registerAnnotations() {
        if(!self::$annotationsRegistered) {
            AnnotationRegistry::registerLoader(array(get_called_class(), 'loadAnnotation'));
            self::$annotationsRegistered = true;
        }
    }

    /**
     * Annotation autoload function
     * @param string $class The class name to load
     * @return bool Return true if the class is loaded, false if the class is not in the namespace "MacFJA\Validator\Annotation" or doesn't exist.
     */
    static public function loadAnnotation($class) {
        $annotationNamespace = __NAMESPACE__.'\\Annotation\\';
        if(strpos(ltrim($class, '\\'), ltrim($annotationNamespace, '\\')) === 0) {
            //Fallback into the default loader chain
            return class_exists($class);
        }
        return false;
    }
}
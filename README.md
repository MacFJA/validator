# Validator #

**Validator** is a simple way to validate a property or an object.

## Features ##

- Can works with annotation
- Easy to create your own validator
- If compatible, the validator can provide a sanitized version of your value/object

## Examples ##

### Validate a value ###

    $myValue = '2014-09-02';
    $validator = new LowerThan();
    $validator->value = new \DateTime('2012-07-01');
    $validator->type = 'date';
    $validator->setInput($myValue);

    if ($validator->isValid()) {
        die('Houston, we've had a problem');
    } else {
        die('You are too old for that');
    }

### Validate an object (method 1) ###

Class Person

    class Person {
        public $firstName = 'John';
        public $lastName = 'Doe';
        public $email = 'jdoe@example.com';
        public $dob;
    }

Somewhere in your code

    $jdoe = new Person();
    $objectValidator = new ObjectValidator($jdoe);

    // - Names validator
    $validator1 = new Length();
    $validator1->type = 'string';
    $validator1->minimum = 1;
    $validator1->maximum = 255;//SQL limit?
    // - email validator
    $validator2 = new Email();
    // - age
    $validator3 = new DateTime();
    $validator4 = new LowerThan();
    $validator4->value = '-18 years';
    $validator4->type = 'date';

    $objectValidator->addValidator('firstName', $validator1);
    $objectValidator->addValidator('lastName', $validator1);
    $objectValidator->addValidator('email', $validator2);
    $objectValidator->addValidator('dob', $validator3);
    $objectValidator->addValidator('dob', $validator4);

    if (!$objectValidator->isValid()) {
        var_dump($objectValidator->getErrors());
    }

### Validate an object (method 2 - annotation) ###

Class Person

    use MacFJA\Validator\Annotation as Validator

    class Person {
        /** @Validator\Length(type="string", minimum=1, maximum=255) */
        public $firstName = 'John';
        /** @Validator\Length(type="string", minimum=1, maximum=255) */
        public $lastName = 'Doe';
        /** @Validator\Email */
        public $email = 'jdoe@example.com';
        /**
          * @Validator\DateTime
          * @Validator\LowerThan("-18 years", type="date")
          */
        public $dob;
    }

Somewhere in your code

    $jdoe = new Person();
    $objectValidator = new AnnotationValidator($jdoe);

    if (!$objectValidator->isValid()) {
        echo sprintf("You have %d error(s) in your object", $objectValidator->getErrorsCount());
    }

## Notice About Annotation ##

For annotation validator, you have to:

 - add Doctrine Annotations library in your project (`doctrine/annotations`).
 - add all annotations into the Doctrine Annotation Registry.

You MUST add all annotations before use it.

To do so you can call the function `AnnotationValidator::registerAnnotations()`.
This function will add all default validator annotations into doctrine annotation registry.
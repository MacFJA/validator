<?php


namespace MacFJA\Tests\Validator;


use Doctrine\Common\Annotations\AnnotationRegistry;
use MacFJA\Validator\Annotation as Validator;
use MacFJA\Validator\AnnotationValidator;

require_once(__DIR__.'/ObjectValidatorTest.php');

class AnnotationValidatorTest extends ObjectValidatorTest {


    static function setUpBeforeClass()
    {
        AnnotationValidator::registerAnnotations();
    }

    protected function getValidatorAndObject($toPopulate) {
        $object = new AnnotationValidatorTestMockClass();

        $funcName = 'populate'.$toPopulate;
        $object->$funcName();

        $objectValidator = new AnnotationValidator($object);

        return array('validator' => $objectValidator, 'object' => $object);
    }
}

class AnnotationValidatorTestMockClass {
    /**
     * @Validator\Length(type="string", minimum=1, maximum=255)
     */
    public $firstName;
    /**
     * @Validator\Length(type="string", minimum=1, maximum=255)
     */
    public $lastName;
    /**
     * @Validator\Email
     */
    public $email;
    /**
     * @Validator\DateTime
     * @Validator\LowerThan("-18 years", type = "date")
     */
    public $dob;

    public function populateJohnDoe() {
        $this->firstName = 'John';
        $this->lastName = 'Doe';
        $this->email = 'jdoe@example.com';
        $this->dob = new \DateTime('1970-01-01');
    }

    public function populateBaby() {
        $this->firstName = 'Joe';
        $this->lastName = 'Little';
        $this->email = 'baby@example.com';
        $this->dob = new \DateTime('-2 weeks');
    }

    public function populateTextDate() {
        $this->firstName = 'Johnny';
        $this->lastName = 'Doe';
        $this->email = 'johnny@example.com';
        $this->dob = '-17 years';
    }

    public static function sanitizeTextDate() {
        $o = new static();
        $o->firstName = 'Johnny';
        $o->lastName = 'Doe';
        $o->email = 'johnny@example.com';
        $o->dob = new \DateTime('-17 years');

        return $o;
    }
}

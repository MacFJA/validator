<?php


namespace MacFJA\Tests\Validator;


use MacFJA\Validator\Annotation\DateTime;
use MacFJA\Validator\Annotation\Email;
use MacFJA\Validator\Annotation\Length;
use MacFJA\Validator\Annotation\LowerThan;
use MacFJA\Validator\ObjectValidator;

class ObjectValidatorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $toPopulate
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testGetErrorsCount($toPopulate, $expected)
    {
        $validator = $this->getValidator($toPopulate);
        $this->assertEquals($expected, $validator->getErrorsCount());
    }

    protected function getValidatorAndObject($toPopulate) {
        $object = new ObjectValidatorTestMockClass();

        $funcName = 'populate'.$toPopulate;
        $object->$funcName();

        $objectValidator = new ObjectValidator($object);

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

        return array('validator' => $objectValidator, 'object' => $object);
    }

    protected function getValidator($toPopulate) {
        $data = $this->getValidatorAndObject($toPopulate);
        return $data['validator'];
    }

    /**
     * @param $toPopulate
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($toPopulate, $expected)
    {
        $objectValidator = $this->getValidator($toPopulate);
        $this->assertEquals($expected, $objectValidator->isValid());
    }

    /**
     * @param $toPopulate
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testGetErrors($toPopulate, $expected)
    {
        $objectValidator = $this->getValidator($toPopulate);
        $this->assertEquals($expected, $objectValidator->getErrors());
    }


    /**
     * @param $toPopulate
     * @param $expected
     * @return mixed
     *
     * @dataProvider dataProvider
     */
    public function testGetSanitizedObject($toPopulate, $expected)
    {
        $data = $this->getValidatorAndObject($toPopulate);
        $validator = $data['validator'];
        /** @var ObjectValidator $validator */

        if($expected == 'same') {
            $this->assertEquals($data['object'], $validator->getSanitizedObject());
        }
        else {
            $this->assertEquals($expected, $validator->getSanitizedObject());
        }
    }

    public function dataProvider($name) {
        if($name == 'testIsValid') {
            return array(
                array('JohnDoe', true),
                array('Baby', false),
                array('TextDate', false),
            );
        }
        if($name == 'testGetErrorsCount') {
            return array(
                array('JohnDoe', 0),
                array('Baby', 1),
                array('TextDate', 1)
            );
        }
        if($name == 'testGetErrors') {
            $twoWeek = new \DateTime('-2 weeks');
            return array(
                array('JohnDoe', array('firstName' => array(), 'lastName' => array(), 'email' => array(), 'dob' => array())),
                array('Baby', array('firstName' => array(), 'lastName' => array(), 'email' => array(), 'dob' => array('"'.$twoWeek->format('c').'" is not lower than "-18 years"'))),
                array('TextDate', array('firstName' => array(), 'lastName' => array(), 'email' => array(), 'dob' => array('"-17 years" is not lower than "-18 years"')))
            );
        }
        if($name == 'testGetSanitizedObject') {
            return array(
                array('JohnDoe', 'same'),
                array('Baby', 'same'),
                array('TextDate', ObjectValidatorTestMockClass::sanitizeTextDate())
            );
        }
    }
}

class ObjectValidatorTestMockClass {
    public $firstName;
    public $lastName;
    public $email;
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

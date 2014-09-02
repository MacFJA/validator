<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\Email;

class EmailTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $expected) {
        $validator = Email::newValidator($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    public function testCanSanitize() {
        $validator = new Email();
        $this->assertFalse($validator->canSanitize());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSanitize() {
        $validator = new Email();
        $validator->sanitize();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $expected) {
        $validator = Email::newValidator($propertyValue);
        $this->assertEquals($propertyValue, $validator->safeSanitize());
    }

    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testErrors($propertyValue, $expected) {
        $validator = Email::newValidator($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid' || $name == 'testSafeSanitize') {
            return array(
                array('"John Doe" <john.doe@example.com>', true),
                array('John <john.doe@example.com>', true),
                array('<john.doe@example.com>', true),
                array('john.doe@example.com', true),

                array('John Doe <john.doe@example.com>', false),
                array('john.doe.@example.com', false),
                array('john.doe@example.com>', false),
                array('jo€hn.doe@example.com', false),
                array('john.doe.@example.com', false),
                array('.john.doe@example.com', false),
                array('<john.doe@example.com', false),
                array('"John <john.doe@example.com>', false),
            );
        }
        if ($name == 'testErrors') {
            return array(
                // Integer
                array('"John Doe" <john.doe@example.com>', array()),
                array(null, array('"" is not a valid email address')),
                array(9, array('"9" is not a valid email address')),
                array('.john.doe@example.com', array('".john.doe@example.com" is not a valid email address')),
                array('john.doe.@example.com', array('"john.doe.@example.com" is not a valid email address')),
            );
        }

        throw new \InvalidArgumentException;
    }
}

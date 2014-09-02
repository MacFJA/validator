<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\Length;
use MacFJA\Validator\Annotation\Between;

class LengthTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $constraintType
     * @param $constraintMin
     * @param $constraintMax
     * @param $expected
     *
     * @internal param $constraintValue
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $constraintType, $constraintMin, $constraintMax, $expected) {
        $validator = new Length();
        $validator->minimum = $constraintMin;
        $validator->maximum = $constraintMax;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    /**
     * @param $propertyValue
     * @param $constraintType
     * @param $constraintMin
     * @param $constraintMax
     * @param $expected
     *
     * @internal param $constraintValue
     * @dataProvider dataProvider
     */
    public function testIsValid2($propertyValue, $constraintType, $constraintMin, $constraintMax, $expected) {
        $validator = new Between();
        $validator->minimum = $constraintMin;
        $validator->maximum = $constraintMax;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    public function testCanSanitize() {
        $validator = new Length();
        $this->assertFalse($validator->canSanitize());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSanitize() {
        $validator = new Length();
        $validator->sanitize();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new Length();
        $validator->setInput($propertyValue);
        $this->assertEquals($propertyValue, $validator->safeSanitize());
    }

    /**
     * @param $propertyValue
     * @param $constraintType
     * @param $constraintValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testErrors($propertyValue, $constraintType, $constraintMin, $constraintMax, $expected) {
        $validator = new Length();
        $validator->minimum = $constraintMin;
        $validator->maximum = $constraintMax;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid' || $name == 'testIsValid2' || $name == 'testSafeSanitize') {
            return array(
                // Integer
                array(11, 'int', 10, 12, true),
                array(11, 'int', '10', '12', true),
                array(10, 'int', 10, 12, false),
                array(12, 'int', 10, 12, false),
                array(7, 'int', 10, 12, false),
                // String
                array('This is a test', 'string', 12, 16, true),
                array('This is a test', 'string', '12', '16', true),
                array('A test', 'string', 12, 16, false),
                array('A very long test string', 'string', 12, 16, false),


                // Float
                array(11.0, 'int', 10, 12, true),
                array(10.1, 'int', 10, 12, true),
                array(10.1, 'int', '10', 12, true),
                array(10.1, 'int', '10.0', 12, true),
                array(10.0, 'int', 10, 12, false),
                array(12.0, 'int', 10, 12, false),
                array(13.0, 'int', 10, 12, false),
                array(9, 'int', 10, 12, false),
                // String as Integer/Float
                array('11.0', 'int', 10, 12, true),
                array('10.1', 'int', 10, 12, true),
                array('10.0', 'int', 10, 12, false),
                array('11', 'int', 10, 12, true),
                array('10', 'int', 10, 12, false),
                array('12', 'int', 10, 12, false),
                array('13', 'int', 10, 12, false),
                array('9', 'int', 10, 12, false),
                // String
                array('ThisIsATest', 'string', 8, 12, true),
                array('ThisIsATest', 'string', '8', '12',  true),
                array('Is False', 'string', 8, 12, false),
                array('NotGood', 'string', 8, 12, false),
                array('A very long test string', 'string', 8, 12, false),
                // Date
                array('now', 'date', 'yesterday', 'tomorrow', true),
                array('yesterday', 'date', 'yesterday', 'tomorrow', false),
                array('+2 days', 'date', 'yesterday', 'tomorrow', false),
            );
        }
        if ($name == 'testErrors') {
            return array(
                array(11, 'int', 10, 12, array()),
                array(9,  'int', 10, 12, array('"9" is not greater than "10"')),
                array(10, 'int', 10, 12, array('"10" is not greater than "10"')),
                array(12, 'int', 10, 12, array('"12" is not lower than "12"')),
                array(13, 'int', 10, 12, array('"13" is not lower than "12"')),
                array('Is False', 'string', 8, 10, array('"Is False" is not greater than "8"')),
                array('A very very long string for testing purpose', 'string', 8, 10, array('"A very very long string for testing purpose" is not lower than "10"')),
                array('+2 days', 'date', 'yesterday', 'tomorrow', array('"+2 days" is not lower than "tomorrow"')),
                array('-2 days', 'date', 'yesterday', 'tomorrow', array('"-2 days" is not greater than "yesterday"')),
            );
        }

        throw new \InvalidArgumentException;
    }
}
<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\GreaterThan;

class GreaterThanTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $constraintType
     * @param $constraintValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new GreaterThan();
        $validator->value = $constraintValue;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    public function testCanSanitize() {
        $validator = new GreaterThan();
        $this->assertFalse($validator->canSanitize());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSanitize() {
        $validator = new GreaterThan();
        $validator->sanitize();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new GreaterThan();
        $validator->value = $constraintValue;
        $validator->type = $constraintType;
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
    public function testErrors($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new GreaterThan();
        $validator->value = $constraintValue;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid' || $name == 'testSafeSanitize') {
            return array(
                // Integer
                array(11, 'int', 10, true),
                array(11, 'int', '10', true),
                array(10, 'int', 10, false),
                array(9, 'int', 10, false),
                // Float
                array(11.0, 'int', 10, true),
                array(10.1, 'int', 10, true),
                array(10.1, 'int', '10', true),
                array(10.1, 'int', '10.0', true),
                array(10.0, 'int', 10, false),
                array(9, 'int', 10, false),
                // String as Integer/Float
                array('11.0', 'int', 10, true),
                array('10.1', 'int', 10, true),
                array('10.0', 'int', 10, false),
                array('11', 'int', 10, true),
                array('10', 'int', 10, false),
                array('9', 'int', 10, false),
                // String
                array('ThisIsATest', 'string', 8, true),
                array('ThisIsATest', 'string', '8', true),
                array('Is False', 'string', 8, false),
                array('NotGood', 'string', 8, false),
                // Date
                array('1970-01-01', 'date', 'yesterday', false),
                array('yesterday', 'date', 'yesterday', false),
                array('now', 'date', 'yesterday', true),
            );
        }
        if ($name == 'testErrors') {
            return array(
                array(11, 'int', 10, array()),
                array(9, 'int', 10, array('"9" is not greater than "10"')),
                array('Is False', 'string', 8, array('"Is False" is not greater than "8"')),
                array('now', 'date', 'tomorrow', array('"now" is not greater than "tomorrow"')),
            );
        }

        throw new \InvalidArgumentException;
    }
}
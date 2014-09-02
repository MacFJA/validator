<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\LowerThan;

class LowerThanTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $constraintType
     * @param $constraintValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new LowerThan();
        $validator->value = $constraintValue;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    public function testCanSanitize() {
        $validator = new LowerThan();
        $this->assertFalse($validator->canSanitize());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testSanitize() {
        $validator = new LowerThan();
        $validator->sanitize();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $constraintType, $constraintValue, $expected) {
        $validator = new LowerThan();
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
        $validator = new LowerThan();
        $validator->value = $constraintValue;
        $validator->type = $constraintType;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid' || $name == 'testSafeSanitize') {
            return array(
                // Integer
                array(9, 'int', 10, true),
                array(9, 'int', '10', true),
                array(10, 'int', 10, false),
                array(11, 'int', 10, false),
                // Float
                array(9.0, 'int', 10, true),
                array(9.9, 'int', 10, true),
                array(9.9, 'int', '10', true),
                array(9.9, 'int', '10.0', true),
                array(10.0, 'int', 10, false),
                array(11, 'int', 10, false),
                // String as Integer/Float
                array('9.0', 'int', 10, true),
                array('9.9', 'int', 10, true),
                array('10.0', 'int', 10, false),
                array('9', 'int', 10, true),
                array('10', 'int', 10, false),
                array('11', 'int', 10, false),
                // String
                array('ATest', 'string', 8, true),
                array('ATest', 'string', '8', true),
                array('Is False', 'string', 8, false),
                array('Not Good at all', 'string', 8, false),
                // Date
                array('yesterday', 'date', 'tomorrow', true),
                array('now', 'date', 'yesterday', false),
                array('yesterday', 'date', 'yesterday', false),
                array('-2 days', 'date', 'yesterday', true),
            );
        }
        if ($name == 'testErrors') {
            return array(
                // Integer
                array(9, 'int', 10, array()),
                array(11, 'int', 10, array('"11" is not lower than "10"')),
                array('Is False', 'string', 8, array('"Is False" is not lower than "8"')),
                array('+2 days', 'date', 'tomorrow', array('"+2 days" is not lower than "tomorrow"')),
            );
        }

        throw new \InvalidArgumentException;
    }
}
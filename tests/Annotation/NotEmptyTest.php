<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\NotEmpty;

class NotEmptyTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $default
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }

    /**
     * @param $propertyValue
     * @param $default
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testCanSanitize($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->canSanitize());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->safeSanitize());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSanitize($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->sanitize());
    }
    /**
     * @dataProvider dataProvider
     *
     * @expectedException \BadMethodCallException
     */
    public function testSanitizeException($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $validator->sanitize();
    }

    /**
     * @param $propertyValue
     * @param $default
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testErrors($propertyValue, $default, $expected) {
        $validator = new NotEmpty();
        $validator->default = $default;
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid') {
            return array(
                array('', null, false),
                array('not Empty', null, true),
                array('', 'default value', false),
                array('not Empty', 'default value', true),
                array(0, 'default value', false),
                array(1, 'default value', true),
                array(false, 'lorem', false),
                array(null, 'ipsum', false),
                array(array(), 'lorem ipsum', false),
            );
        }
        if ($name == 'testCanSanitize') {
            return array(
                array('', null, false),
                array('not Empty', null, false),
                array('', 'default value', true),
                array('not Empty', 'default value', false),
                array(0, 'default value', true),
                array(1, 'default value', false),
                array(false, 'lorem', true),
                array(null, 'ipsum', true),
                array(array(), 'lorem ipsum', true),
            );
        }
        if ($name == 'testSafeSanitize') {
            return array(
                array('', null, ''),
                array('not Empty', null, 'not Empty'),
                array('', 'default value', 'default value'),
                array('not Empty', 'default value', 'not Empty'),
                array(0, 'default value', 'default value'),
                array(1, 'default value', 1),
                array(false, 'lorem', 'lorem'),
                array(null, 'ipsum', 'ipsum'),
                array(array(), 'lorem ipsum', 'lorem ipsum'),
            );
        }
        if ($name == 'testSanitizeException') {
            return array(
                array('', null, ''),
            );
        }
        if ($name == 'testSanitize') {
            return array(
                array('not Empty', null, 'not Empty'),
                array('', 'default value', 'default value'),
                array('not Empty', 'default value', 'not Empty'),
                array(0, 'default value', 'default value'),
                array(1, 'default value', 1),
                array(false, 'lorem', 'lorem'),
                array(null, 'ipsum', 'ipsum'),
                array(array(), 'lorem ipsum', 'lorem ipsum'),
            );
        }
        if ($name == 'testErrors') {
            return array(
                array('not Empty', null, array()),
                array('', 'default value', array('The value cannot be empty')),
                array('not Empty', 'default value', array()),
                array(0, 'default value', array('The value cannot be empty')),
                array(1, 'default value', array()),
                array(false, 'lorem', array('The value cannot be empty')),
                array(null, 'ipsum', array('The value cannot be empty')),
                array(array(), 'lorem ipsum', array('The value cannot be empty')),
            );
        }

        throw new \InvalidArgumentException;
    }
}

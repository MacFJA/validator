<?php


namespace MacFJA\Tests\Validator\Annotation;


use MacFJA\Validator\Annotation\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testIsValid($propertyValue, $expected) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->isValid());
    }
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testCanSanitize($propertyValue, $expected) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->canSanitize());
    }
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testSafeSanitize($propertyValue, $expected) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->safeSanitize());
    }
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testSanitize($propertyValue, $expected) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->sanitize());
    }
    /**
     * @param $propertyValue
     *
     * @dataProvider dataProvider
     * @expectedException \BadMethodCallException
     */
    public function testSanitizeException($propertyValue) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $validator->sanitize();
    }
    /**
     * @param $propertyValue
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testErrors($propertyValue, $expected) {
        $validator = new DateTime();
        $validator->setInput($propertyValue);
        $this->assertEquals($expected, $validator->errors());
    }

    public function dataProvider($name) {
        if ($name == 'testIsValid') {
            return array(
                array('', true),
                array(new \DateTime(), true),
                array('@1', true),
                array('now', true),
                array('&é"', false),
                array(array(), false),
                array(null, false),
                array(1, false),
                array(false, false),
                array('+2 days', true),
                array('2014-08-24 23:15:16', true),
                array('99999-08-24 23:15:16', true),
            );
        }
        if ($name == 'testCanSanitize') {
            return array(
                array('', true),
                array(new \DateTime(), false),
                array('@1', true),
                array('now', true),
                array(array(), false),
                array(null, false),
                array(1, false),
                array(false, false),
                array('+2 days', true),
                array('2014-08-24 23:15:16', true),
                array('99999-08-24 23:15:16', true),
            );
        }
        if ($name == 'testSafeSanitize') {
            return array(
                array('', new \DateTime()),
                array(new \DateTime(), new \DateTime()),
                array('@1', new \DateTime('@1')),
                array('now', new \DateTime()),
                array(array(), array()),
                array(null, null),
                array(1, 1),
                array(false, false),
                array('+2 days', new \DateTime('+2 days')),
                array('2014-08-24 23:15:16', new \DateTime('2014-08-24 23:15:16')),
                array('99999-08-24 23:15:16', new \DateTime('99999-08-24 23:15:16')),
            );
        }
        if ($name == 'testSanitizeException') {
            return array(
                array(array()),
                array(new \DateTime(), new \DateTime()),
                array(null),
                array(1),
                array(false),
            );
        }
        if ($name == 'testSanitize') {
            return array(
                array('', new \DateTime()),
                array('@1', new \DateTime('@1')),
                array('now', new \DateTime()),
                array('+2 days', new \DateTime('+2 days')),
                array('2014-08-24 23:15:16', new \DateTime('2014-08-24 23:15:16')),
                array('99999-08-24 23:15:16', new \DateTime('99999-08-24 23:15:16')),
            );
        }
        if ($name == 'testErrors') {
            return array(
                array('+2 days', array()),
                array(array(), array('The value can not be convert into a "\DateTime" object')),
                array(null, array('The value can not be convert into a "\DateTime" object')),
                array(1, array('The value can not be convert into a "\DateTime" object')),
                array(false, array('The value can not be convert into a "\DateTime" object')),
            );
        }

        throw new \InvalidArgumentException;
    }
}

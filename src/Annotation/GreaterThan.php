<?php

namespace MacFJA\Validator\Annotation;

/**
 * Class GreaterThan
 *
 * Compare property value. Check if value is greater than the value defined.
 * Compatible data type:<ul><li>int</li>
 * <li>string <i>(check string length)</i></li>
 * <li>datetime, time, date</li></ul>
 *
 * For <tt>datetime</tt>, <tt>time</tt>, <tt>date</tt> inputs can be string.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Model\Validator\Annotation
 */
class GreaterThan extends AbstractValidator
{
    /**
     * @Enum({"int","string","datetime","time","date"})
     * @var string
     */
    public $type = 'int';
    /**
     * @Require
     * @var mixed
     */
    public $value = 0;

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $input = $this->getInput();
        switch ($this->type) {
            case 'string':
                return strlen($input) > $this->value;
            case 'time':
            case 'date':
            case 'datetime':
                $date = new \DateTime(($input instanceof \DateTime) ? $input->format('c') : $input);
                $toCompareTo = new \DateTime($this->value);
                return $date->getTimestamp() > $toCompareTo->getTimestamp();
            case 'int':
            default:
                return $input > $this->value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canSanitize()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function errors()
    {
        if (!$this->isValid()) {
            $value = $this->getInput() instanceof \DateTime?$this->getInput()->format('c'):$this->getInput();
            return array('"' . $value . '" is not greater than "' . $this->value . '"');
        }
        return array();
    }
}
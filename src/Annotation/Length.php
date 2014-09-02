<?php

namespace MacFJA\Validator\Annotation;

/**
 * Class Length
 *
 * Check if the property value is between 2 defined value
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Model\Validator\Annotation
 */
class Length extends AbstractValidator
{
    public $minimum = 0;
    public $maximum = PHP_INT_MAX;
    /**
     * @Enum({"int","string","datetime","time","date"})
     * @var string
     */
    public $type = 'int';

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $minimumValidator = new GreaterThan();
        $minimumValidator->type = $this->type;
        $minimumValidator->value = $this->minimum;
        $minimumValidator->setInput($this->getInput());
        $maximumValidator = new LowerThan();
        $maximumValidator->type = $this->type;
        $maximumValidator->value = $this->maximum;
        $maximumValidator->setInput($this->getInput());

        return ($minimumValidator->isValid() && $maximumValidator->isValid());
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
        $errors = array();
        $minimumValidator = new GreaterThan();
        $minimumValidator->type = $this->type;
        $minimumValidator->value = $this->minimum;
        $minimumValidator->setInput($this->getInput());
        $maximumValidator = new LowerThan();
        $maximumValidator->type = $this->type;
        $maximumValidator->value = $this->maximum;
        $maximumValidator->setInput($this->getInput());

        $errors = array_merge($errors, $minimumValidator->errors());
        $errors = array_merge($errors, $maximumValidator->errors());

        return $errors;
    }
}
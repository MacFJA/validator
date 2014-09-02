<?php

namespace MacFJA\Validator\Annotation;

/**
 * Class NotEmpty
 *
 * Test if the property is empty (see http://php.net/manual/en/function.empty.php and http://php.net/manual/en/types.comparisons.php).
 * If the property is empty and a default value is provided, then on sanitize action, the property value will be the default value.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author MacFJA
 * @package App\Model\Validator\Annotation
 */
class NotEmpty extends AbstractValidator
{
    public $default;

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $input = $this->getInput();
        return !empty($input);
    }

    /**
     * {@inheritdoc}
     */
    public function canSanitize()
    {
        return !$this->isValid() && !empty($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function sanitize()
    {
        if ($this->isValid()) {
            return $this->getInput();
        } elseif ($this->canSanitize()) {
            return $this->default;
        } else {
            return parent::sanitize();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function errors()
    {
        if ($this->isValid()) {
            return array();
        }
        return array('The value cannot be empty');
    }
}
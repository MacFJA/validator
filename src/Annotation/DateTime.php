<?php

namespace MacFJA\Validator\Annotation;

/**
 * Class DateTime
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Model\Validator\Annotation
 */
class DateTime extends AbstractValidator
{

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        if (is_string($this->getInput())) {
            try {
                new \DateTime($this->getInput());
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return ($this->getInput() instanceof \DateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function canSanitize()
    {
        return $this->isValid() && !($this->getInput() instanceof \DateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function sanitize()
    {
        if ($this->canSanitize()) {
            return new \DateTime($this->getInput());
        }
        return parent::sanitize();
    }

    /**
     * {@inheritdoc}
     */
    public function errors()
    {
        if (!$this->isValid()) {
            return array('The value can not be convert into a "\DateTime" object');
        }
        return array();
    }
}
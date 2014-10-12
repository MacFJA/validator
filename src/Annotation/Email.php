<?php

namespace MacFJA\Validator\Annotation;

/**
 * Class Email
 *
 * Validate an email address against the RFC-2822 chapter 3.4.1
 * @see https://tools.ietf.org/html/rfc2822#section-3.4.1
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @package App\Model\Validator\Annotation
 */
class Email extends AbstractValidator
{

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $localChar = '[a-zA-Z0-9!#$%&\'*+\\/=?^_`{}|~-]';
        $domainChar = '[\x20-\x5A\x5E-\x7E]';
        $emailAddressRegEx = $localChar . '+(\.' . $localChar . '+)*@' . $domainChar . '+(\.' . $domainChar . '+)?\.[a-zA-Z]{2,}';

        $quoteEmail = '/^".[^"]+"\s*<' . $emailAddressRegEx . '>$/';
        $noQuoteEmail = '/^([!\x23-\x5A\x5E-\x7E]+\s*)?<' . $emailAddressRegEx . '>$/';
        $onlyAddress = '/^' . $emailAddressRegEx . '$/';

        $input = $this->getInput();

        if (preg_match($quoteEmail, $input)) {
            return true;
        } else if (preg_match($noQuoteEmail, $input)) {
            return true;
        } else {
            return preg_match($onlyAddress, $input) != 0;
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
            return array('"' . $this->getInput() . '" is not a valid email address');
        }
        return array();
    }
}
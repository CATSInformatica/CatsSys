<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Validator;

use Zend\Validator\AbstractValidator;

class Cpf extends AbstractValidator
{

    const INVALID_NUMBERS = "InvalidCPFNumbers";
    const INVALID_FORMAT = "InvalidCPFFormat";

    /**
     * Validation failure message template definitions
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID_NUMBERS => "Invalid Cpf",
        self::INVALID_FORMAT => "Invalid format. The only valid format is XXX.XXX.XXX-XX",
    );

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  string $value
     * @return boolean
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        if (!$this->respectsRegularExpression($value)) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        $cpf = $this->trimCPF($value);

        if (!$this->applyingCpfRules($cpf)) {
            $this->error(self::INVALID_NUMBERS);
            return false;
        }
        return true;
    }

    /**
     * @param $cpf
     * @return string
     */
    private function trimCPF($cpf)
    {
        return preg_replace('/[.,-]/', '', $cpf);
    }

    /**
     * @param $cpf
     * @return bool
     */
    private function respectsRegularExpression($cpf)
    {
        $regularExpression = "[0-9]{3}\\.[0-9]{3}\\.[0-9]{3}-[0-9]{2}";
        if (!preg_match('#' . $regularExpression . '#', $cpf)) {
            return false;
        }
        return true;
    }

    /**
     * @param $cpf
     * @return bool
     */
    private function applyingCpfRules($cpf)
    {
        if (
                strlen($cpf) != 11 || $cpf == "00000000000" || $cpf == "11111111111" ||
                $cpf == "22222222222" || $cpf == "33333333333" || $cpf == "44444444444" ||
                $cpf == "55555555555" || $cpf == "66666666666" || $cpf == "77777777777" ||
                $cpf == "88888888888" || $cpf == "99999999999"
        ) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i ++) {
            $sum += (int) (substr($cpf, $i, 1)) * (10 - $i);
        }
        $rmdr = 11 - ($sum % 11);
        if ($rmdr == 10 || $rmdr == 11) {
            $rmdr = 0;
        }
        if ($rmdr != (int) (substr($cpf, 9, 1))) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 10; $i ++) {
            $sum += (int) (substr($cpf, $i, 1)) * (11 - $i);
        }
        $rmdr = 11 - ($sum % 11);
        if ($rmdr == 10 || $rmdr == 11) {
            $rmdr = 0;
        }
        if ($rmdr != (int) (substr($cpf, 10, 1))) {
            return false;
        }
        return true;
    }

}

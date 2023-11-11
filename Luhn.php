<?php

/**
 * @Package:    PHP Fuse - Luhn algorith
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\Validate;

use PHPFuse\Validate\ValidVatFormat;

class Luhn
{
    private $number;
    private $length;
    private $string;
    private $part;

    /**
     * Start intsance and input Value
     */
    public function __construct($number)
    {
        preg_match('/^[a-zA-Z\d]+$/', $number, $this->string);

        $this->string = preg_replace('/[^A-Z\d]/', '', strtoupper($number));
        $this->number = preg_replace('/\D/', '', $number);
        $this->length = strlen($this->number);
    }

    /**
     * Validate Swedish security number
     * @return bool
     */
    public function socialNumber(): bool
    {
        $this->part = $this->part();
        if (in_array('', $this->part, true)) {
            return false;
        }

        if (!$this->isDate() && !$this->isCoordinationNumber()) {
            return false;
        }

        $checkStr = $this->part['year'] . $this->part['month'] . $this->part['day'] . $this->part['num'];
        $sum = $this->luhn($checkStr);
        return ((int)$sum === (int)$this->part['check']);
    }

    /**
     * Check if a Swedish social security number is for a male.
     * @return bool
     */
    public function isMale(): bool
    {
        $this->part = $this->part();
        $genderDigit = substr($this->part['num'], -1);
        return boolval($genderDigit % 2);
    }

    /**
     * Check if a Swedish social security number is for a female.
     * @return bool
     */
    public function isFemale(): bool
    {
        return !$this->isMale();
    }

    /**
     * Validate Swedish security number
     * @return bool
     */
    public function personnummer(): bool
    {
        return $this->socialNumber();
    }

    /**
     * Get org. number
     * @return bool
     */
    public function orgNumber(): bool
    {
        $num = substr($this->number, 0, 10);
        $sum = $this->luhn($num);
        return (bool)((int)$sum === 0);
    }

    /*
    function plusgiro() {
        $sum = substr($this->number, 0, -1);
        $check = substr($this->number, -1);
        $sum = $this->luhn($sum);
        return ((int)$sum === (int)$check);
    }
    function bankgiro() {
        $sum = $this->luhn("50334143");
        return ((int)$sum === (int)$check);
    }
     */

    /**
     * Is valid creditcard number
     * @return bool
     */
    public function creditcard(): bool
    {
        if ($this->cardPrefix()) {
            $sum = $this->luhn($this->number);
            return (bool)((int)$sum === 0);
        }
        return false;
    }

    /**
     * Get card type
     * @return string|bool
     */
    public function cardPrefix(): string|bool
    {
        $arr = [
            'visaelectron' => '/^4(026|17500|405|508|844|91[37])/',
            'maestro' => '/^(5(018|0[23]|[68])|6(39|7))/',
            'forbrugsforeningen' => '/^600/',
            'dankort' => '/^5019/',
            'visa' => '/^4/',
            'mastercard' => '/^(5[0-5]|2[2-7])/',
            'amex' => '/^3[47]/',
            'dinersclub' => '/^3[0689]/',
            'discover' => '/^6([045]|22)/',
            'unionpay' => '/^(62|88)/',
            'jcb' => '/^35/'
        ];

        foreach ($arr as $card => $pattern) {
            if (preg_match($pattern, $this->number)) {
                return $card;
            }
        }
        return false;
    }

    /**
     * Validate Vat
     * @return bool
     */
    public function vatNumber(): bool
    {
        $vat = new ValidVatFormat($this->string);
        if ($vat->validate()) {
            if ($vat->countryCode() === "SE") {
                return $this->orgNumber();
            }
            return true;
        }

        return false;
    }

    /**
     * Chech if is a date
     * @return boolean
     */
    public function isDate(): bool
    {
        return checkdate(
            $this->getPart('month'),
            $this->getPart('day'),
            $this->getPart('century') . $this->getPart('year')
        );
    }

    /**
     * Check if is a coordinaion number
     * If you are going to live and work here but don’t meet the requirements
     * for registering in the Swedish Population Register.
     * @return bool
     */
    public function isCoordinationNumber()
    {
        return checkdate(
            $this->getPart('month'),
            ((int)$this->getPart('day') - 60),
            $this->getPart('century') . $this->getPart('year')
        );
    }

    /**
     * The Luhn algorithm.
     * @param string str
     * @return int
     */
    final protected function luhn($number)
    {
        $sum = $val = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $val = (int)$number[$i];
            $val *= 2 - ($i % 2);
            if ($val > 9) {
                $val -= 9;
            }
            $sum += $val;
        }

        return (ceil($sum / 10) * 10 - $sum);
    }

    /**
     * Parse Swedish social security numbers and get the parts
     * @param string $str
     * @return array
     */
    final protected function part()
    {

        $reg = '/^(\d{2}){0,1}(\d{2})(\d{2})(\d{2})([\+\-\s]?)(\d{3})(\d)$/';
        preg_match($reg, $this->number, $match);
        if (!isset($match) || count($match) !== 8) {
            return array();
        }

        $century = $match[1];
        $year    = $match[2];
        $month   = $match[3];
        $day     = $match[4];
        $sep     = $match[5];
        $num     = $match[6];
        $check   = $match[7];

        if (!in_array($sep, array('-', '+'))) {
            if (empty($century) || date('Y') - intval(strval($century) . strval($year)) < 100) {
                $sep = '-';
            } else {
                $sep = '+';
            }
        }
        if (empty($century)) {
            if ($sep === '+') {
                $baseYear = date('Y', strtotime('-100 years'));
            } else {
                $baseYear = date('Y');
            }
            $century = substr(($baseYear - (($baseYear - $year) % 100)), 0, 2);
        }

        return array(
            'century' => $century,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'sep' => $sep,
            'num' => $num,
            'check' => $check
        );
    }

    /**
     * Get part
     * @param  string $key
     * @return string
     */
    final protected function getPart(string $key): ?string
    {
        return ($this->part[$key] ??  "0");
    }
}

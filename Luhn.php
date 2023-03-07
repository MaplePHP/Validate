<?php 
/**
 * @Package: 	PHP Fuse - Luhn algorith
 * @Author: 	Daniel Ronkainen
 * @Licence: 	The MIT License (MIT), Copyright © Daniel Ronkainen
 				Don't delete this comment, its part of the license.
 * @Version: 	1.0.0
 */

namespace Validate;

class Luhn {


    private $_number;
    private $_length;
    private $_string;


    /**
     * Start intsance and input Value
     */
    function __construct($number) {
        preg_match('/^[a-zA-Z\d]+$/', $number, $this->_string);

        $this->_string = preg_replace('/[^A-Z\d]/', '', strtoupper($number)); 
        $this->_number = preg_replace('/\D/', '', $number);
        $this->_length = strlen($this->_number);
    }

    /**
     * Validate Swedish security number
     * @return bool
     */
    function socialNumber() {
        $this->_part = $this->_part();
        if(in_array('', $this->_part, true)) return false;

        if(!$this->isDate() && !$this->isCoordinationNumber()) return false;

        $checkStr = $this->_part['year'] . $this->_part['month'] . $this->_part['day'] . $this->_part['num'];
        $sum = $this->_luhn($checkStr);
        return ((int)$sum === (int)$this->_part['check']);
    }

    /**
     * Check if a Swedish social security number is for a male.
     * @return bool
     */
    function isMale() {
        $this->_part = $this->_part();
        $genderDigit = substr($this->_part['num'], -1);
        return boolval($genderDigit % 2);
    }

    /**
     * Check if a Swedish social security number is for a female.
     * @return bool
     */
    function isFemale() {
        return !$this->isMale();
    }

    /**
     * Validate Swedish security number
     * @return bool
     */
    function personnummer() {
        return $this->socialNumber();
    }

    function orgNumber() {
        $num = substr($this->_number, 0, 10);
        $sum = $this->_luhn($num);
        return (bool)((int)$sum === 0);
    }

    /*
    function plusgiro() {
        $sum = substr($this->_number, 0, -1);
        $check = substr($this->_number, -1);
        $sum = $this->_luhn($sum);
        return ((int)$sum === (int)$check);
    }
    function bankgiro() {
        $sum = $this->_luhn("50334143");
        return ((int)$sum === (int)$check);
    }
     */

    // BEHÖVS TESTAS
    function creditcard() {
        if($this->cardPrefix()) {
            $sum = $this->_luhn($this->_number);
            return (bool)((int)$sum === 0);
        }
        return false;
    }

    function cardPrefix() {

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

        foreach($arr as $card => $pattern) {
            if(preg_match($pattern, $this->_number)) return $card;
        }

        return false;
    }


    // FLYTTA TILL NYTT RAMVERK (VISSA LÄNDER HAR CHECK DIGITS ex. MOD 11-2)
    function vatNumber() {
        $vat = new validVatFormat($this->_string);
        if($vat->validate()) {
            if($vat->countryCode() === "SE") {
                return $this->orgNumber();
            }
            return true;
        }

        return false;
    }


    /**
     * The Luhn algorithm.
     * @param string str
     * @return int
     */
    protected function _luhn($number) {
        $sum = $v = 0;
        for ($i = 0; $i < strlen($number); $i ++) {
            $v = (int)$number[$i];
            $v *= 2 - ($i % 2);
            if($v > 9) $v -= 9;
            $sum += $v;
        }

        return (ceil($sum / 10) * 10 - $sum);
    }

    /**
     * Format Swedish social security numbers to official format
     * @param string|int $str
     * @param bool $longFormat YYMMDD-XXXX or YYYYMMDDXXXX since the tax office says both are official
     * @return string
     */
    function format($str, $longFormat = false) {
        if(!$this->validate($str)) return '';
        
        $parts = $this->_part($str);
        $format = ($longFormat) ? '%1$s%2$s%3$s%4$s%6$s%7$s' : '%2$s%3$s%4$s%5$s%6$s%7$s';

        $return = sprintf($format, $parts['century'], $parts['year'], $parts['month'], $parts['day'], $parts['sep'], $parts['num'], $parts['check']);
        return $return;
    }

    function isDate() {
        return checkdate($this->getPart('month'), $this->getPart('day'), $this->getPart('century').$this->getPart('year'));
    }

    function getPart($k) {
        return ($this->_part[$k] ??  0);
    }

    /**
     * Check if is a coordinaion number
     * If you are going to live and work here but don’t meet the requirements for registering in the Swedish Population Register.
     * @return bool
     */
    function isCoordinationNumber() {
        return checkdate($this->getPart('month'), ((int)$this->getPart('day')-60), $this->getPart('century').$this->getPart('year'));
    }

    
    /**
     * Parse Swedish social security numbers and get the parts
     * @param string $str
     * @return array
     */
    private function _part() {

        $reg = '/^(\d{2}){0,1}(\d{2})(\d{2})(\d{2})([\+\-\s]?)(\d{3})(\d)$/';
        preg_match($reg, $this->_number, $match);
        if(!isset($match) || count($match) !== 8) return array();

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
        if(empty($century)) {
            if($sep === '+') {
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


}

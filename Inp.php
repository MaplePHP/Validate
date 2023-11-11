<?php

/**
 * @Package:    PHP Fuse - Input validation library
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\Validate;

use PHPFuse\Validate\Interfaces\InpInterface;
use DateTime;
use PHPFuse\Validate\Luhn;

class Inp implements InpInterface
{
    private $value;
    private $length;
    private $dateTime;
    private $luhn;

    /**
     * Start instance
     * @param  string $value the input value
     * @return self
     */
    public static function value(string $value): self
    {
        $inst = new self();
        $inst->value = $value;
        $inst->length = $inst->getLength($value);
        $inst->dateTime = new DateTime("now");
        return $inst;
    }

    /**
     * Get value string length
     * @param  string $value
     * @return int
     */
    public function getLength(string $value): int
    {
        return strlen($value);
    }

    /**
     * Access luhn validation class
     * @return Luhn
     */
    public function luhn(): Luhn
    {
        if (is_null($this->luhn)) {
            $this->luhn = new Luhn($this->value);
        }
        return $this->luhn;
    }

    /**
     * Will check if value if empty (e.g. "", 0, NULL) = false
     * @return bool
     */
    public function required(): bool
    {
        if ($this->length(1) && !empty($this->value)) {
            return true;
        }
        return false;
    }

    /**
     * Will only check if there is a value (e.g. 0) = true
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->length(1);
    }

    /**
     * Validate Swedish personal numbers
     * @return bool
     */
    public function socialNumber(): bool
    {
        return $this->luhn()->personnummer();
    }

    /**
     * Validate Swedish personal numbers
     * @return bool
     */
    public function personnummer(): bool
    {
        return $this->socialNumber();
    }

    /**
     * Validate Swedish org numbers
     * @return bool
     */
    public function orgNumber(): bool
    {
        return $this->luhn()->orgNumber();
    }

    /**
     * Validate creditcardnumbers (THIS needs to be tested)
     * @return bool
     */
    public function creditcard(): bool
    {
        return $this->luhn()->creditcard();
    }

    /**
     * Validate Swedish vat number
     * @return bool
     */
    public function vatNumber(): bool
    {
        return $this->luhn()->vatNumber();
    }

    /**
     * Validate email
     * Loosely check if is email. By loosley I mean it will not check if valid DNS. You can check this
     * manually with the method @dns but in most cases this will not be necessary.
     * @return bool
     */
    public function email(): bool
    {
        return (bool)(filter_var($this->value, FILTER_VALIDATE_EMAIL));
    }

    /**
     * Find in string
     * @param  string   $match keyword to match agains
     * @param  int|null $pos   match start positon if you want
     * @return bool
     */
    public function findInString(string $match, ?int $pos = null): bool
    {
        return (bool)((is_null($pos) && strpos($this->value, $match) !== false) ||
            (!is_null($pos) && strpos($this->value, $match) === $pos));
    }

    /**
     * Alternative "Find in string" @findInString()
     */
    public function strpos(string $match, ?int $pos = null): bool
    {
        $match = (is_null($pos)) ? false : $pos;
        return (bool)((is_null($pos) && strpos($this->value, $match) !== $pos) ||
            (!is_null($pos) && strpos($this->value, $match) === $pos));
    }


    /**
     * Check if is phone
     * @return bool
     */
    public function phone(): bool
    {
        $val = str_replace([" ", "-", "—", "–", "(", ")"], ["", "", "", "", "", ""], $this->value);
        $match = preg_match('/^[0-9]{7,14}+$/', $val);
        $strict = preg_match('/^\+[0-9]{1,2}[0-9]{6,13}$/', $val);
        return (bool)($strict || $match);
    }

    /**
     * Check if is valid ZIP
     * @param  int      $arg1 start length
     * @param  int|null $arg2 end length
     * @return bool
     */
    public function zip(int $arg1, int $arg2 = null): bool
    {
        $this->value = str_replace([" ", "-", "—", "–"], ["", "", "", ""], $this->value);
        $this->length = $this->getLength($this->value);
        return (bool)($this->int() && $this->length($arg1, $arg2));
    }

    /**
     * Value is number
     * @return bool
     */
    public function number(): bool
    {
        return (bool)(is_numeric($this->value));
    }

    public function numeric(): bool
    {
        return $this->number();
    }

    public function numericVal(): bool
    {
        return $this->number();
    }

    /**
     * Value is number positive 20
     * @return bool
     */
    public function positive(): bool
    {
        return (bool)((float)$this->value >= 0);
    }

    /**
     * Value is number negative -20
     * @return bool
     */
    public function negative(): bool
    {
        return (bool)((float)$this->value < 0);
    }

    /**
     * Value is minimum float|int value
     * @return bool
     */
    public function min(float $int): bool
    {
        return (bool)((float)$this->value >= $int);
    }

    /**
     * Value is minimum float|int value (Same as "@min()" but can be used to add another error message)
     * @return bool
     */
    public function minAlt(float $int): bool
    {
        return $this->min($int);
    }

    /**
     * Value is maximum float|int value
     * @return bool
     */
    public function max(float $int): bool
    {
        return (bool)((float)$this->value <= $int);
    }

    /**
     * Is value float
     * @return bool
     */
    public function float(): bool
    {
        return (bool)filter_var($this->value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Is value int
     * @return bool
     */
    public function int(): bool
    {
        return (bool)filter_var($this->value, FILTER_VALIDATE_INT);
    }

    /**
     * Value string length is more than start ($arg1) or between start ($arg1) and end ($arg2)
     * @param  int      $arg1 start length
     * @param  int|null $arg2 end length
     * @return bool
     */
    public function length(int $arg1, int $arg2 = null): bool
    {
        if ($this->length >= $arg1 && (($arg2 === null) || $this->length <= $arg2)) {
            return true;
        }
        return false;
    }

    /**
     * Value string length of OTHER field is more than start ($arg1) or between start ($arg1) and end ($arg2)
     * @param  string   $key    HTTP Post KEY
     * @param  int      $arg1      start length
     * @param  int|null $arg2      end length
     * @return bool
     */
    public function hasLength(string $key, int $arg1, int $arg2 = null): bool
    {
        $post = ($_POST[$key] ?? 0);
        $continue = (bool)((int)$post === 1);
        return (bool)(!$continue || $this->length($arg1, $arg2));
    }

    /**
     * Value string length is equal to ($arg1)
     * @param  int  $arg1  length
     * @return bool
     */
    public function equalLength(int $arg1): bool
    {
        if ($this->length === $arg1) {
            return true;
        }
        return false;
    }

    /**
     * IF value equals to param
     * @return bool
     */
    public function equal($str): bool
    {
        return (bool)((string)$this->value === (string)$str);
    }

    /**
     * IF value equals to param
     * @return bool
     */
    public function notEqual($str): bool
    {
        return (bool)((string)$this->value !== (string)$str);
    }

    /**
     * Chech is a valid version number
     * @return bool
     */
    public function validVersion($strict = false): bool
    {
        $strictMatch = (!$strict || preg_match("/^(\d?\d)\.(\d?\d)\.(\d?\d)$/", (string)$this->value));
        return (bool)($strictMatch && version_compare((string)$this->value, '0.0.1', '>=') >= 0);
    }

    /**
     * Validate/compare if a version is equal/more/equalMore/less... e.g than withVersion
     * @param  string $withVersion [description]
     * @param  string $operator    [description]
     * @return bool
     */
    public function versionCompare(string $withVersion, string $operator = ">="): bool
    {
        return (bool)(version_compare((string)$this->value, $withVersion, $operator) >= 0);
    }

    /**
     * Is value string
     * @return bool
     */
    public function string(): bool
    {
        return (bool)(is_string($this->value));
    }

    /**
     * Lossy password - Will return false if a character inputed is not allowed
     * [a-zA-Z\d$@$!%*?&] - Matches "any" letter (uppercase or lowercase), digit, or special character
     * from the allowed set of special characters
     * @param  integer $length Minimum length
     * @return [type]          [description]
     */
    public function lossyPassword($length = 1): bool
    {
        return (bool)preg_match('/^[a-zA-Z\d$@$!%*?&]{' . $length . ',}$/', $this->value);
    }

    /**
     * Strict password
     * (?=.*[a-z]) - at least one lowercase letter
     * (?=.*[A-Z]) - at least one uppercase letter
     * (?=.*\d) - at least one digit
     * (?=.*[$@$!%*?&]) - at least one special character from the set: $, @, #, !, %, *, ?, &
     * [A-Za-z\d$@$!%*?&]{1,} - matches 1 or more characters consisting of letters, digits,
     * and the allowed special characters
     * I do tho recomend that you validate the length with @length(8, 60) method!
     * @param  integer $length Minimum length
     * @return bool
     */
    public function strictPassword($length = 1): bool
    {
        return (bool)preg_match(
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{' . $length . ',}$/',
            $this->value
        );
    }

    /**
     * Is value is string and character between a-z or A-Z
     * @return bool
     */
    public function pregMatch($matchStr): bool
    {
        return (bool)preg_match("/^[" . $matchStr . "]+$/", $this->value);
    }


    /**
     * Is value is string and character between a-z or A-Z
     * @return bool
     */
    public function atoZ(): bool
    {
        return (bool)preg_match("/^[a-zA-Z]+$/", $this->value);
    }

    /**
     * Is value is string and character between a-z (LOWERCASE)
     * @return bool
     */
    public function lowerAtoZ(): bool
    {
        return (bool)preg_match("/^[a-z]+$/", $this->value);
    }

    /**
     * Is value is string and character between A-Z (UPPERCASE)
     * @return bool
     */
    public function upperAtoZ(): bool
    {
        return (bool)preg_match("/^[A-Z]+$/", $this->value);
    }


    /**
     * Is Hex color code string
     * @return bool
     */
    public function hex(): bool
    {
        return preg_match('/^#([0-9A-F]{3}){1,2}$/i', $this->value);
    }

    /**
     * Is value array
     * @return bool
     */
    public function isArray(): bool
    {
        return (bool)(is_array($this->value));
    }

    /**
     * Is value object
     * @return bool
     */
    public function isObject(): bool
    {
        return (bool)(is_object($this->value));
    }

    /**
     * Is value bool
     * @return bool
     */
    public function bool(): bool
    {
        return (bool)(is_bool($this->value));
    }

    /**
     * If value === ([on, off], [yes, no], [1, 0] or [true, false])
     * @return bool
     */
    public function boolVal(): bool
    {
        $val = strtolower(trim((string)$this->value));
        return (bool)($val === "on" || $val === "yes" || $val === "1" || $val === "true");
    }

    /**
     * Is value between two other values (1-10, a-z, 1988-08-01-1988-08-10)
     * @param  int|float|string|DateTime $arg1 10, a, 1988-08-01
     * @param  int|float|string|DateTime $arg2 20, z, 1988-08-20
     * @return bool
     */
    public function between(int|float|string|DateTime $arg1, int|float|string|DateTime $arg2): bool
    {

        if ($this->number()) {
            return ($this->min($arg1) && $this->max($arg2));
        } elseif (strlen($arg1) === 1 && strlen($arg2) === 1) {
            $range = $this->rangeBetween(strtolower($arg1), strtolower($arg2));
            $length = count($range);
            if ($find = array_search((string)$this->value, $range)) {
                return (bool)(($find + 1) <= $length);
            }
        } elseif ($this->date() || $this->dateTime()) {
            $date = new DateTime($this->value);
            $fromDate = new DateTime($arg1);
            $toDate = new DateTime($arg2);
            return (bool)($date >= $fromDate && $date <= $toDate);
        }

        return false;
    }

    /**
     * Check if is a date
     * @param  string $format validate after this date format (default Y-m-d)
     * @return bool|inst(dateTime)
     */
    public function date($format = "Y-m-d"): bool|DateTime
    {
        return DateTime::createFromFormat($format, $this->value);
    }


    /**
     * Check if is a date and time
     * @param  string  $format  validate after this date format (default Y-m-d H:i)
     * @return bool|inst(dateTime)
     */
    public function dateTime($format = "Y-m-d H:i"): bool|DateTime
    {
        return $this->date($format);
    }

    /**
     * Check if is a date and time
     * @param  string  $format  validate after this date format (default Y-m-d H:i)
     * @return bool|inst(dateTime)
     */
    public function time($format = "H:i"): bool|DateTime
    {
        return $this->date($format);
    }

    /**
     * Check if is a date and a "valid range"
     * @param  string $format validate after this date format (default Y-m-d H:i)
     * @return bool / array(T1, T2); T1 = start and T2 = end
     */
    public function dateRange($format = "Y-m-d H:i"): bool
    {
        $exp = explode(" - ", $this->value);
        if (count($exp) === 2) {
            $time1 = trim($exp[0]);
            $time2 = trim($exp[1]);
            $val1 = DateTime::createFromFormat($format, $time1);
            $val2 = DateTime::createFromFormat($format, $time2);
            return (bool)(($val1 && $val2 && ($val1->getTimestamp() <= $val2->getTimestamp())) ?
                ["t1" => $time1, "t2" => $time2] : false);
        }
        return false;
    }

    /**
     * Check "minimum" age (value format should be validate date "Y-m-d")
     * @param  int    $arg1 18 == user should be atleast 18 years old
     * @return [type]    [description]
     */
    public function age(int $arg1): bool
    {
        $now = $this->dateTime->format("Y");
        $dateTime = new \DateTime($this->value);
        $birth = $dateTime->format("Y");
        $age = (int)($now - $birth);
        return (bool)($age >= (int)$arg1);
    }

    /**
     * Check if is valid domain
     * @param  boolean $flag stricter = true
     * @return bool
     */
    public function domain($flag = true): bool
    {
        $flag = ($flag) ? FILTER_FLAG_HOSTNAME : false;
        return (bool)filter_var((string)$this->value, FILTER_VALIDATE_DOMAIN, $flag);
    }

    /**
     * Check if is valid URL (http|https is required)
     * @return bool
     */
    public function url(): bool
    {
        $val = (string)$this->value;

        // Only used to pass validation will not change any data
        $val = str_replace(['{{root}}', '{{url}}'], ["https://example.se", "https://example.se/"], $val);
        $val = str_replace(["å", "ä", "ö"], ["a", "a", "o"], strtolower($val));

        return (bool)filter_var($val, FILTER_VALIDATE_URL);
    }

    /**
     * Check if "Host|domain" has an valid DNS (will check A, AAAA and MX)
     * @psalm-suppress UndefinedConstant
     * @return bool
     */
    public function dns(): bool
    {
        $host = $this->value;
        $Aresult = true;
        if (!defined('INTL_IDNA_VARIANT_2003')) {
            define('INTL_IDNA_VARIANT_2003', 0);
        }
        $variant = (defined('INTL_IDNA_VARIANT_UTS46')) ? INTL_IDNA_VARIANT_UTS46 : INTL_IDNA_VARIANT_2003;
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
        $MXresult = checkdnsrr($host, 'MX');
        if (!$MXresult) {
            $Aresult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
        }
        return (bool)($MXresult || $Aresult);
    }

    /**
     * Match DNS record by search for TYPE and matching VALUE
     * @param  int $type   (DNS_A, DNS_CNAME, DNS_HINFO, DNS_CAA, DNS_MX, DNS_NS, DNS_PTR, DNS_SOA,
     * DNS_TXT, DNS_AAAA, DNS_SRV, DNS_NAPTR, DNS_A6, DNS_ALL or DNS_ANY)
     * @return false/array
     */
    public function matchDNS(int $type): bool|array
    {
        $host = $this->value;
        if (!defined('INTL_IDNA_VARIANT_2003')) {
            define('INTL_IDNA_VARIANT_2003', 0);
        }
        $variant = INTL_IDNA_VARIANT_2003;
        if (defined('INTL_IDNA_VARIANT_UTS46')) {
            $variant = INTL_IDNA_VARIANT_UTS46;
        }
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
        $Aresult = dns_get_record($host, $type);
        if (is_array($Aresult) && count($Aresult) > 0) {
            return $Aresult;
        }
        return false;
    }

    /**
     * Validate multiple. Will return true if "one" matches
     * @param  array $arr [description]
     * @return mixed
     */
    public function oneOf(array $arr)
    {
        $valid = false;
        foreach ($arr as $val) {
            if (is_array($val)) {
                if (call_user_func_array(['self', 'length'], $val)) {
                    $valid = true;
                }
            } else {
                if ($this->{$val}()) {
                    $valid = true;
                }
            }
        }
        return $valid;
    }

    /**
     * Validate multiple. Will return true if "all" matches
     * @param  array $arr [description]
     * @return mixed
     */
    public function allOf(array $arr)
    {
        $valid = true;
        foreach ($arr as $val) {
            if (is_array($val)) {
                if (!call_user_func_array(['self', 'length'], $val)) {
                    $valid = false;
                }
            } else {
                if (!$this->{$val}()) {
                    $valid = false;
                }
            }
        }
        return $valid;
    }

    public function continue(array $arr1, array $arr2)
    {
        if ($this->allOf($arr1)) {
            if (!$this->required()) {
                return true;
            }
            return $this->allOf($arr2);
        }
        return false;
    }

    // For your information: ÅÄÖ will not be in predicted range.
    private function rangeBetween($start, $end)
    {
        $result = array();
        list(, $_start, $_end) = unpack("N*", mb_convert_encoding($start . $end, "UTF-32BE", "UTF-8"));
        $offset = $_start < $_end ? 1 : -1;
        $current = $_start;
        while ($current != $_end) {
            $result[] = mb_convert_encoding(pack("N*", $current), "UTF-8", "UTF-32BE");
            $current += $offset;
        }
        $result[] = $end;
        return $result;
    }
}

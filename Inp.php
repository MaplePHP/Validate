<?php

/**
 * @Package:    MaplePHP - Input validation library
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Validate;

use ErrorException;
use Exception;
use MaplePHP\DTO\MB;
use MaplePHP\Validate\Interfaces\InpInterface;
use MaplePHP\DTO\Format\Str;
use DateTime;

class Inp implements InpInterface
{
    public const WHITELIST_OPERATORS = [
        '!=',
        '<',
        '<=',
        '<>',
        '=',
        '==',
        '>',
        '>=',
        'eq',
        'ge',
        'gt',
        'le',
        'lt',
        'ne'
    ];

    private mixed $value;
    private int $length = 0;
    private DateTime $dateTime;
    private ?Luhn $luhn = null;
    private ?Str $getStr = null;


    /**
     * Start instance
     * @param mixed $value the input value
     * @throws ErrorException
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
        $this->dateTime = new DateTime("now");
        if(is_string($value) || is_numeric($value)) {
            $this->length = $this->getLength((string)$value);
            $this->getStr = new Str($this->value);
        }
    }

    /**
     * Immutable: Validate against new value
     * @param mixed $value
     * @return InpInterface
     */
    public function withValue(mixed $value): InpInterface
    {
        $inst = clone $this;
        $inst->value = $value;
        return $inst;
    }

    /**
     * Start instance
     * @param string $value the input value
     * @return self
     * @throws ErrorException
     */
    public static function value(mixed $value): self
    {
        return new self($value);
    }

    /**
     * Get value string length
     * @param string $value
     * @return int
     * @throws ErrorException
     */
    public function getLength(string $value): int
    {
        $mb = new MB($value);
        return (int)$mb->strlen();
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
    public function personalNumber(): bool
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
     * Validate credit card numbers (THIS needs to be tested)
     * @return bool
     */
    public function creditCard(): bool
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
     * Loosely check if is email. By loosely I mean it will not check if valid DNS. You can check this
     * manually with the method @dns but in most cases this will not be necessary.
     * @return bool
     */
    public function email(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * Find in string
     * @param  string   $match keyword to match against
     * @param  int|null $pos   match start position if you want
     * @return bool
     */
    public function findInString(string $match, ?int $pos = null): bool
    {
        return ((is_null($pos) && str_contains($this->value, $match)) ||
                (strpos($this->value, $match) === $pos));
    }

    /**
     * Check if is a phone number
     * @return bool
     */
    public function phone(): bool
    {
        if (is_null($this->getStr)) {
            return false;
        }
        $val = (string)$this->getStr->replace([" ", "-", "—", "–", "(", ")"], ["", "", "", "", "", ""]);
        $match = preg_match('/^[0-9]{7,14}+$/', $val);
        $strict = preg_match('/^\+[0-9]{1,2}[0-9]{6,13}$/', $val);
        return ($strict || $match);
    }

    /**
     * Check if is valid ZIP
     * @param int $arg1 start length
     * @param int|null $arg2 end length
     * @return bool
     * @throws ErrorException
     */
    public function zip(int $arg1, int $arg2 = null): bool
    {
        if (is_null($this->getStr)) {
            return false;
        }
        $this->value = (string)$this->getStr->replace([" ", "-", "—", "–"], ["", "", "", ""]);
        $this->length = $this->getLength($this->value);
        return ($this->isInt() && $this->length($arg1, $arg2));
    }

    /**
     * Is value float
     * Will validate whether a string is a valid float (User input is always a string)
     * @return bool
     */
    public function isFloat(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_FLOAT) !== false);
    }

    /**
     * Is value int
     * Will validate whether a string is a valid integer (User input is always a string)
     * @return bool
     */
    public function isInt(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_INT) !== false);
    }

    /**
     * Is value string
     * @return bool
     */
    public function isString(): bool
    {
        return is_string($this->value);
    }

    /**
     * Is value string
     * @return bool
     */
    public function isStr(): bool
    {
        return $this->isString();
    }

    /**
     * Is value array
     * @return bool
     */
    public function isArray(): bool
    {
        return is_array($this->value);
    }

    /**
     * Is value object
     * @return bool
     */
    public function isObject(): bool
    {
        return is_object($this->value);
    }

    /**
     * Is value bool
     * @return bool
     */
    public function isBool(): bool
    {
        return (is_bool($this->value));
    }

    /**
     * Check if the value itself can be Interpreted as a bool value
     * E.g. If value === ([on, off], [yes, no], [1, 0] or [true, false])
     * @return bool
     */
    public function isBoolVal(): bool
    {
        $val = strtolower(trim((string)$this->value));
        $true = ($val === "on" || $val === "yes" || $val === "1" || $val === "true");
        $false = ($val === "off" || $val === "no" || $val === "0" || $val === "false");
        return ($true || $false);
    }

    /**
     * Is null
     * @return bool
     */
    public function isNull(): bool
    {
        return is_null($this->value);
    }

    /**
     * Is file
     * @return bool
     */
    public function isFile(): bool
    {
        return is_file($this->value);
    }

    /**
     * Is directory
     * @return bool
     */
    public function isDir(): bool
    {
        return is_dir($this->value);
    }

    /**
     * Is resource
     * @return bool
     */
    public function isResource(): bool
    {
        return is_resource($this->value);
    }

    /**
     * Is writable
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->value);
    }

    /**
     * Is readable
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->value);
    }

    /**
     * Value is number
     * @return bool
     */
    public function number(): bool
    {
        return (is_numeric($this->value));
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
        return ((float)$this->value >= 0);
    }

    /**
     * Value is number negative -20
     * @return bool
     */
    public function negative(): bool
    {
        return ((float)$this->value < 0);
    }

    /**
     * Value is minimum float|int value
     * @param float $int
     * @return bool
     */
    public function min(float $int): bool
    {
        return ((float)$this->value >= $int);
    }

    /**
     * Value is minimum float|int value (Same as "@min()" but can be used to add another error message)
     * @param float $int
     * @return bool
     */
    public function minAlt(float $int): bool
    {
        return $this->min($int);
    }

    /**
     * Value is maximum float|int value
     * @param float $int
     * @return bool
     */
    public function max(float $int): bool
    {
        return ((float)$this->value <= $int);
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
     * @param $str
     * @return bool
     */
    public function equal($str): bool
    {
        return ($this->value === $str);
    }

    /**
     * IF value equals to param
     * @param $str
     * @return bool
     */
    public function notEqual($str): bool
    {
        return ($this->value !== $str);
    }

    /**
     * Check is a valid version number
     * @param bool $strict (validate as a semantic Versioning, e.g. 1.0.0)
     * @return bool
     */
    public function validVersion(bool $strict = false): bool
    {
        $strictMatch = (!$strict || preg_match("/^(\d?\d)\.(\d?\d)\.(\d?\d)$/", (string)$this->value));
        $compare = version_compare((string)$this->value, '0.0.1', '>=');
        return ($strictMatch && $compare !== false && $compare >= 0);
    }

    /**
     * Validate/compare if a version is equal/more/equalMore/less... e.g than withVersion
     * @param string $withVersion
     * @param '!='|'<'|'<='|'<>'|'='|'=='|'>'|'>='|'eq'|'ge'|'gt'|'le'|'lt'|'ne' $operator
     * @return bool
     */
    public function versionCompare(string $withVersion, string $operator = "=="): bool
    {
        if (in_array($operator, self::WHITELIST_OPERATORS)) {
            return version_compare((string)$this->value, $withVersion, $operator);
        }
        return false;
    }

    /**
     * Lossy password - Will return false if a character inputted is not allowed
     * [a-zA-Z\d$@$!%*?&] - Matches "any" letter (uppercase or lowercase), digit, or special character
     * from the allowed set of special characters
     * @param integer $length Minimum length
     * @return bool
     */
    public function lossyPassword(int $length = 1): bool
    {
        return ((int)preg_match('/^[a-zA-Z\d$@$!%*?&]{' . $length . ',}$/', $this->value) > 0);
    }

    /**
     * Strict password
     * (?=.*[a-z]) - at least one lowercase letter
     * (?=.*[A-Z]) - at least one uppercase letter
     * (?=.*\d) - at least one digit
     * (?=.*[$@$!%*?&]) - at least one special character from the set: $, @, #, !, %, *, ?, &
     * [A-Za-z\d$@$!%*?&]{1,} - matches 1 or more characters consisting of letters, digits,
     * and the allowed special characters
     * I do tho recommend that you validate the length with @length(8, 60) method!
     * @param integer $length Minimum length
     * @return bool
     */
    public function strictPassword(int $length = 1): bool
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{' . $length . ',}$/';
        return ((int)preg_match($pattern, $this->value) > 0);
    }

    /**
     * Is value is string and character between a-z or A-Z
     * @param $matchStr
     * @return bool
     */
    public function pregMatch($matchStr): bool
    {
        return ((int)preg_match("/^[" . $matchStr . "]+$/", $this->value) > 0);
    }


    /**
     * Is value is string and character between a-z or A-Z
     * @return bool
     */
    public function atoZ(): bool
    {
        return ((int)preg_match("/^[a-zA-Z]+$/", $this->value) > 0);
    }

    /**
     * Is value is string and character between a-z (LOWERCASE)
     * @return bool
     */
    public function lowerAtoZ(): bool
    {
        return ((int)preg_match("/^[a-z]+$/", $this->value) > 0);
    }

    /**
     * Is value is string and character between A-Z (UPPERCASE)
     * @return bool
     */
    public function upperAtoZ(): bool
    {
        return ((int)preg_match("/^[A-Z]+$/", $this->value) > 0);
    }

    /**
     * Is Hex color code string
     * @return bool
     */
    public function hex(): bool
    {
        return ((int)preg_match('/^#([0-9A-F]{3}){1,2}$/i', $this->value) > 0);
    }

    /**
     * Check if is a date
     * @param string $format validate after this date format (default Y-m-d)
     * @return bool
     */
    public function date(string $format = "Y-m-d"): bool
    {
        return (DateTime::createFromFormat($format, $this->value) !== false);
    }


    /**
     * Check if is a date and time
     * @param string $format  validate after this date format (default Y-m-d H:i)
     * @return bool
     */
    public function dateTime(string $format = "Y-m-d H:i"): bool
    {
        return $this->date($format);
    }

    /**
     * Check if is a date and time
     * @param string $format  validate after this date format (default Y-m-d H:i)
     * @return bool
     */
    public function time(string $format = "H:i"): bool
    {
        return $this->date($format);
    }

    /**
     * Check if is a date and a "valid range"
     * @param string $format validate after this date format (default Y-m-d H:i)
     * @return array|false E.g. array(T1, T2); T1 = start and T2 = end
     */
    public function dateRange(string $format = "Y-m-d H:i"): array|false
    {
        $exp = explode(" - ", $this->value);
        if (count($exp) === 2) {
            $time1 = trim($exp[0]);
            $time2 = trim($exp[1]);
            $val1 = DateTime::createFromFormat($format, $time1);
            $val2 = DateTime::createFromFormat($format, $time2);
            return (($val1 && $val2 && ($val1->getTimestamp() <= $val2->getTimestamp())) ?
                ["t1" => $time1, "t2" => $time2] : false);
        }
        return false;
    }

    /**
     * Check "minimum" age (value format should be validated date "Y-m-d")
     * @param int $arg1  18: user should be 18 or older
     * @return bool
     * @throws Exception
     */
    public function age(int $arg1): bool
    {
        $now = (int)$this->dateTime->format("Y");
        $dateTime = new DateTime($this->value);
        $birth = (int)$dateTime->format("Y");
        $age = ($now - $birth);
        return ($age <= $arg1);
    }

    /**
     * Check if is valid domain
     * @param  bool $strict stricter = true
     * @return bool
     */
    public function domain(bool $strict = true): bool
    {
        $strict = ($strict) ? FILTER_FLAG_HOSTNAME : 0;
        return (filter_var((string)$this->value, FILTER_VALIDATE_DOMAIN, $strict) !== false);
    }

    /**
     * Check if is valid URL (http|https is required)
     * @return bool
     */
    public function url(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Check if "Host|domain" has an valid DNS (will check A, AAAA and MX)
     * @psalm-suppress UndefinedConstant
     * @noinspection PhpComposerExtensionStubsInspection
     * @return bool
     */
    public function dns(): bool
    {
        $AResult = true;
        $host = $this->getHost($this->value);
        $MXResult = checkdnsrr($host); // Argument 2 is MX by default
        if (!$MXResult) {
            $AResult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
        }
        return ($MXResult || $AResult);
    }

    /**
     * Match DNS record by search for TYPE and matching VALUE
     * @param  int $type   (DNS_A, DNS_CNAME, DNS_HINFO, DNS_CAA, DNS_MX, DNS_NS, DNS_PTR, DNS_SOA,
     * DNS_TXT, DNS_AAAA, DNS_SRV, DNS_NAPTR, DNS_A6, DNS_ALL or DNS_ANY)
     * @noinspection PhpComposerExtensionStubsInspection
     * @return array|false
     */
    public function matchDNS(int $type): array|false
    {
        $host = $this->getHost($this->value);
        $result = dns_get_record($host, $type);
        if (is_array($result) && count($result) > 0) {
            return $result;
        }
        return false;
    }

    /**
     * Get hosts (used for DNS checks)
     * @noinspection PhpComposerExtensionStubsInspection
     * @param string $host
     * @return string
     */
    private function getHost(string $host): string
    {
        if (!defined('INTL_IDNA_VARIANT_2003')) {
            define('INTL_IDNA_VARIANT_2003', 0);
        }
        $variant = (defined('INTL_IDNA_VARIANT_UTS46')) ? INTL_IDNA_VARIANT_UTS46 : INTL_IDNA_VARIANT_2003;
        return rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';
    }

    /**
     * Validate multiple. Will return true if "one" matches
     * @param array $arr
     * @return bool
     * @throws ErrorException
     */
    public function oneOf(array $arr): bool
    {
        $valid = false;
        foreach ($arr as $method => $args) {
            $inst = new self($this->value);
            if(call_user_func_array([$inst, $method], $args)) {
                $valid = true;
            }
        }
        return $valid;
    }

    /**
     * Validate multiple. Will return true if "all" matches
     * @param array $arr
     * @return bool
     * @throws ErrorException
     */
    public function allOf(array $arr): bool
    {
        foreach ($arr as $method => $args) {
            $inst = new self($this->value);
            if(!call_user_func_array([$inst, $method], $args)) {
                return false;
            }
        }
        return true;
    }
}

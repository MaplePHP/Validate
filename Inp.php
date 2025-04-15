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
use MaplePHP\DTO\Format\Arr;
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
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
        $this->dateTime = new DateTime("now");
        $this->init();
    }

    private function init(): void
    {
        if(is_string($this->value) || is_numeric($this->value)) {
            $this->length = $this->getLength((string)$this->value);
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
     * Makes it possible to travers to a value in array or object
     *
     * @param string $key
     * @param bool $immutable
     * @return self
     * @throws ErrorException
     */
    public function traverse(string $key, bool $immutable = true): self
    {
        $value = $this->value;
        if(is_array($this->value) || is_object($this->value)) {
            $value = $this->traversDataFromStr($this->value, $key);
            if(!$immutable && $value !== false) {
                $this->value = $value;
                $this->init();
                return $this;
            }
        }
        return self::value($value);
    }

    /**
     * This will make it possible to validate arrays and object with one line
     *
     * @example validateInData(user.name, 'length', [1, 200]);
     * @param string $key
     * @param string $validate
     * @param array $args
     * @return mixed
     * @throws ErrorException
     */
    public function validateInData(string $key, string $validate, array $args = []): bool
    {
        $inp = $this->traverse($key, false);
        if(!method_exists($inp, $validate)) {
            throw new \BadMethodCallException("Method '{$validate}' does not exist in " . __CLASS__ . " class.");
        }
        return $inp->{$validate}(...$args);
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
     * Get the current value
     * _The value can be changes with travers method and this lets you peak at the new one_
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
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
    public function zip(int $arg1, ?int $arg2 = null): bool
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
     * Is a valid json string
     * @return bool
     */
    public function isJson(): bool
    {
        json_decode($this->value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate a string as html, check that it contains doctype, html, head and body
     * @return bool
     */
    public function isFullHtml(): bool
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        if (!is_string($this->value) || !$dom->loadHTML($this->value, LIBXML_NOERROR | LIBXML_NOWARNING)) {
            return false; // Invalid HTML syntax
        }
        if (!$dom->doctype || strtolower($dom->doctype->name) !== "html") {
            return false;
        }
        $htmlTag = $dom->getElementsByTagName("html")->length > 0;
        $headTag = $dom->getElementsByTagName("head")->length > 0;
        $bodyTag = $dom->getElementsByTagName("body")->length > 0;
        return $htmlTag && $headTag && $bodyTag;
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
    public function isNumber(): bool
    {
        return (is_numeric($this->value));
    }

    // Alias
    public function number(): bool
    {
        return $this->number();
    }

    /**
     * Value is number positive 20
     * @return bool
     */
    public function isPositive(): bool
    {
        return ((float)$this->value >= 0);
    }

    public function positive(): bool
    {
        return $this->isPositive();
    }

    /**
     * Value is number negative -20
     * @return bool
     */
    public function isNegative(): bool
    {
        return ((float)$this->value < 0);
    }

    public function negative(): bool
    {
        return $this->isNegative();
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
    public function length(int $arg1, ?int $arg2 = null): bool
    {
        if ($this->length >= $arg1 && (($arg2 === null) || $this->length <= $arg2)) {
            return true;
        }
        return false;
    }

    /**
     * Check if array is empty
     *
     * @return bool
     */
    public function isArrayEmpty(): bool
    {
        return ($this->isArray() && count($this->value) === 0);
    }

    /**
     * Check if all items in array is truthy
     *
     * @param string|int|float $key
     * @return bool
     */
    public function itemsAreTruthy(string|int|float $key): bool
    {
        if($this->isArray()) {
            $count = Arr::value($this->value)
                ->filter(fn ($item) => $item->flatten()->{$key}->toBool())
                ->count();
            return ($count === count($this->value));
        }
        return false;
    }

    /**
     * Check if truthy item exist in array
     *
     * @param string|int|float $key
     * @return bool
     */
    public function hasTruthyItem(string|int|float $key): bool
    {
        if($this->isArray()) {
            $count = Arr::value($this->value)
                ->filter(fn ($item) => $item->flatten()->{$key}->toBool())
                ->count();
            return ($count > 0);
        }
        return false;
    }

    /**
     * Validate array length equal to
     *
     * @param int $length
     * @return bool
     */
    public function isCountEqualTo(int $length): bool
    {
        return ($this->isArray() && count($this->value) === $length);
    }

    /**
     * Validate array length is more than
     *
     * @param int $length
     * @return bool
     */
    public function isCountMoreThan(int $length): bool
    {
        return ($this->isArray() && count($this->value) > $length);
    }

    /**
     * Validate array length is less than
     *
     * @param int $length
     * @return bool
     */
    public function isCountLessThan(int $length): bool
    {
        return ($this->isArray() && count($this->value) < $length);
    }


    /**
     * Check int value is equal to int value
     * @param int $value
     * @return bool
     */
    public function toIntEqual(int $value): bool
    {
        return (int)$this->value === $value;
    }

    /**
     * Value string length is equal to ($length)
     * @param  int  $length
     * @return bool
     */
    public function isLengthEqualTo(int $length): bool
    {
        if ($this->length === $length) {
            return true;
        }
        return false;
    }

    public function equalLength(int $length): bool
    {
        return $this->isLengthEqualTo($length);
    }

    /**
     * IF value equals to param
     * @param mixed $value
     * @return bool
     */
    public function isEqualTo(mixed $value): bool
    {
        return ($this->value === $value);
    }

    // Alias
    public function equal(mixed $value): bool
    {
        return $this->isEqualTo($value);
    }

    /**
     * IF value equals to param
     * @param mixed $value
     * @return bool
     */
    public function isNotEqualTo(mixed $value): bool
    {
        return ($this->value !== $value);
    }

    public function notEqual(mixed $value): bool
    {
        return $this->isNotEqualTo($value);
    }

    /**
     * IF value is less than to parameter
     * @param float|int $num
     * @return bool
     */
    public function isLessThan(float|int $num): bool
    {
        return ($this->value < $num);
    }

    // Shortcut
    public function lessThan(float|int $num): bool
    {
        return $this->isLessThan($num);
    }

    /**
     * IF value is more than to parameter
     * @param float|int $num
     * @return bool
     */
    public function isMoreThan(float|int $num): bool
    {
        return ($this->value > $num);
    }

    // Alias
    public function moreThan(float|int $num): bool
    {
        return $this->isMoreThan($num);
    }

    /**
     * Checks if a string contains a given substring
     *
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return str_contains($this->value, $needle);
    }

    /**
     * Checks if a string starts with a given substring
     *
     * @param string $needle
     * @return bool
     */
    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->value, $needle);
    }

    /**
     * Checks if a string ends with a given substring
     *
     * @param string $needle
     * @return bool
     */
    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->value, $needle);
    }

    /**
     * Check is a valid version number
     * @param bool $strict (validate as a semantic Versioning, e.g. 1.0.0)
     * @return bool
     */
    public function isValidVersion(bool $strict = false): bool
    {
        $strictMatch = (!$strict || preg_match("/^(\d?\d)\.(\d?\d)\.(\d?\d)$/", (string)$this->value));
        $compare = version_compare((string)$this->value, '0.0.1', '>=');
        return ($strictMatch && $compare !== false && $compare >= 0);
    }

    public function validVersion(bool $strict = false): bool
    {
        return $this->isValidVersion();
    }

    /**
     * Validate/compare if a version is equal/more/equalMore/less... e.g than withVersion
     * @param string $withVersion
     * @param string $operator '!='|'<'|'<='|'<>'|'='|'=='|'>'|'>='|'eq'|'ge'|'gt'|'le'|'lt'|'ne'
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
        return ($age >= $arg1);
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

    // Move Helper function to new file later on

    /**
     * Will make it possible to traverse validation
     *
     * This is a helper function that
     * @param array $array
     * @param string $key
     * @return mixed
     */
    private function traversDataFromStr(array $array, string $key): mixed
    {
        $new = $array;
        $exp = explode(".", $key);
        foreach ($exp as $index) {
            $data = is_object($new) ? ($new->{$index} ?? null) : ($new[$index] ?? null);
            if(is_null($data)) {
                $new = false;
                break;
            }
            $new = $data;
        }
        return $new;
    }
}

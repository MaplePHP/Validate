<?php
/**
 * @Package:    MaplePHP - Input validation library
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Validate;

use ErrorException;
use MaplePHP\DTO\Traverse;
use MaplePHP\Validate\Interfaces\InpInterface;
use MaplePHP\Validate\Traits\InpAliases;
use MaplePHP\DTO\MB;
use MaplePHP\DTO\Format\Str;
use MaplePHP\DTO\Format\Arr;
use DateTime;

class Inp implements InpInterface
{
    use InpAliases;
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
    private ?DNS $dns = null;
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
        $this->init();
    }

    /**
     * Used to reset length in traverse with "mutable" flag
     * @return void
     * @throws ErrorException
     */
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
    public function eq(string $key, bool $immutable = true): self
    {
        $value = $this->value;
        if(is_array($this->value) || is_object($this->value)) {
            $value = Traverse::value($this->value)->eq($key)->get();
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
        $inp = $this->eq($key, false);
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
     * Access DNS validations
     *
     * @return DNS
     */
    public function dns(): DNS
    {
        if(is_null($this->dns)) {
            $this->dns = new DNS($this->value);
        }
        return $this->dns;
    }

    /**
     * Will check if value if empty (e.g. "", 0, NULL) = false
     * @return bool
     */
    public function isRequired(): bool
    {
        if ($this->length(1) && !empty($this->value)) {
            return true;
        }
        return false;
    }

    /**
     * Strict data type validation check if is false
     *
     * @return bool
     */
    public function isTrue(): bool
    {
        return $this->value === true;
    }

    /**
     * Flexible data type validation check if is truthy
     *
     * @return bool
     */
    public function isTruthy(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }

    /**
     * Strict data type validation check if is false
     *
     * @return bool
     */
    public function isFalse(): bool
    {
        return $this->value === false;
    }

    /**
     * Flexible data type validation check if is falsy
     *
     * @return bool
     */
    public function isFalsy(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === false;
    }

    /**
     * Strict data type validation check if value exists in given array
     *
     * @param array $haystack
     * @return bool
     */
    public function isInArray(array $haystack): bool
    {
        return in_array($this->value, $haystack, true);
    }

    /**
     * Flexible data type validation check if value exists in given array
     *
     * @param array $haystack
     * @return bool
     */
    public function isLooselyInArray(array $haystack): bool
    {
        return in_array($this->value, $haystack);
    }

    /**
     * Strict data type validation check if key exists in array
     *
     * @param string|int $key
     * @return bool
     */
    public function keyExists(string|int $key): bool
    {
        return is_array($this->value) && array_key_exists($key, $this->value);
    }

    /**
     * Will only check if there is a value
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->length(1);
    }

    /**
     * Validate Swedish personal numbers (personalNumber)
     * @return bool
     */
    public function isSocialNumber(): bool
    {
        return $this->luhn()->personnummer();
    }

    /**
     * Validate Swedish org numbers
     * @return bool
     */
    public function isOrgNumber(): bool
    {
        return $this->luhn()->orgNumber();
    }

    /**
     * Validate credit card numbers (THIS needs to be tested)
     * @return bool
     */
    public function isCreditCard(): bool
    {
        return $this->luhn()->creditCard();
    }

    /**
     * Validate Swedish vat number
     * @return bool
     */
    public function isVatNumber(): bool
    {
        return $this->luhn()->vatNumber();
    }

    /**
     * Validate email
     * Loosely check if is email. By loosely I mean it will not check if valid DNS. You can check this
     * manually with the method @dns but in most cases this will not be necessary.
     *
     * @return bool
     */
    public function isEmail(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false);
    }


    /**
     * Validate if the email is deliverable
     * This checks if the email is syntactically valid and has a valid MX record.
     *
     * @return bool
     */
    public function isDeliverableEmail(): bool
    {
        return ($this->isEmail() && $this->dns()->isMxRecord());
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
     * Find in string
     *
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
    public function isPhone(): bool
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
     *
     * @param int $minLength start length
     * @param int|null $maxLength end length
     * @return bool
     * @throws ErrorException
     */
    public function isZip(int $minLength, ?int $maxLength = null): bool
    {
        if (is_null($this->getStr)) {
            return false;
        }
        $this->value = (string)$this->getStr->replace([" ", "-", "—", "–"], ["", "", "", ""]);
        $this->length = $this->getLength($this->value);
        return ($this->isInt() && $this->length($minLength, $maxLength));
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
     * Is resource
     * @return bool
     */
    public function isResource(): bool
    {
        return is_resource($this->value);
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
     * Is directory
     * @return bool
     */
    public function isDir(): bool
    {
        return is_dir($this->value);
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
     * Check if is file or directory
     * @return bool
     */
    function isFileOrDirectory(): bool
    {
        return file_exists($this->value);
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
     * Value is strictly a number (int or float).
     *
     * @return bool
     */
    public function isNumber(): bool
    {
        return $this->isFloat() || $this->isInt();
    }

    /**
     * Value is loosely numeric (e.g. numeric strings, scientific notation).
     *
     * @return bool
     */
    public function isNumbery(): bool
    {
        return is_numeric($this->value);
    }

    /**
     * Value is number positive 20
     *
     * @return bool
     */
    public function isPositive(): bool
    {
        return ((float)$this->value >= 0);
    }

    /**
     * Value is number negative -20
     *
     * @return bool
     */
    public function isNegative(): bool
    {
        return ((float)$this->value < 0);
    }

    /**
     * Value is minimum float|int value
     *
     * @param float $int
     * @return bool
     */
    public function min(float $int): bool
    {
        return ((float)$this->value >= $int);
    }

    /**
     * Value is maximum float|int value
     *
     * @param float $int
     * @return bool
     */
    public function max(float $int): bool
    {
        return ((float)$this->value <= $int);
    }

    /**
     * Check if string length is more than start ($min), or between ($min) and ($max)
     *
     * @param  int      $min start length
     * @param  int|null $max end length
     * @return bool
     */
    public function length(int $min, ?int $max = null): bool
    {
        if ($this->length >= $min && (($max === null) || $this->length <= $max)) {
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

    /**
     * Strict data type validation check if equals to expected value
     *
     * @param mixed $expected
     * @return bool
     */
    public function isEqualTo(mixed $expected): bool
    {
        return $this->value === $expected;
    }

    /**
     * Flexible data type validation check if loosely equals to expected value
     *
     * @param mixed $expected
     * @return bool
     */
    public function isLooselyEqualTo(mixed $expected): bool
    {
        return $this->value == $expected;
    }


    /**
     * Strict data type validation check if not equals to expected value
     *
     * @param mixed $value
     * @return bool
     */
    public function isNotEqualTo(mixed $value): bool
    {
        return ($this->value !== $value);
    }

    /**
     * Flexible data type validation check if loosely not equals to expected value
     *
     * @param mixed $value
     * @return bool
     */
    public function isLooselyNotEqualTo(mixed $value): bool
    {
        return ($this->value !== $value);
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

    /**
     * IF value is more than to parameter
     * @param float|int $num
     * @return bool
     */
    public function isMoreThan(float|int $num): bool
    {
        return ($this->value > $num);
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
    public function isLossyPassword(int $length = 1): bool
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
    public function isStrictPassword(int $length = 1): bool
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{' . $length . ',}$/';
        return ((int)preg_match($pattern, $this->value) > 0);
    }

    /**
     * Check if the value contains only characters matching the given pattern.
     *
     * @param string $charRange A character range (e.g., 'a-z', 'A-Z0-9')
     * @return bool
     */
    public function isMatchingPattern(string $charRange): bool
    {
        return (bool)preg_match("/^[$charRange]+$/", $this->value);
    }

    /**
     * Check if the value contains only alphabetic characters (a–z or A–Z).
     *
     * @return bool
     */
    public function isAlpha(): bool
    {
        return (bool)preg_match("/^[a-zA-Z]+$/", $this->value);
    }

    /**
     * Check if the value contains only lowercase letters (a–z).
     *
     * @return bool
     */
    public function isLowerAlpha(): bool
    {
        return (bool)preg_match("/^[a-z]+$/", $this->value);
    }

    /**
     * Check if the value contains only uppercase letters (A–Z).
     *
     * @return bool
     */
    public function isUpperAlpha(): bool
    {
        return (bool)preg_match("/^[A-Z]+$/", $this->value);
    }

    /**
     * Is Hex color code string
     * @return bool
     */
    public function isHex(): bool
    {
        return ((int)preg_match('/^#([0-9A-F]{3}){1,2}$/i', $this->value) > 0);
    }

    /**
     * Check if is a date
     * @param string $format validate after this date format (default Y-m-d)
     * @return bool
     */
    public function isDate(string $format = "Y-m-d"): bool
    {
        return (DateTime::createFromFormat($format, $this->value) !== false);
    }

    /**
     * Check if is a date and time
     * @return bool
     */
    public function isDateWithTime(): bool
    {
        return $this->date("Y-m-d H:i:s");
    }

    /**
     * Check if is a date and time
     * @param bool $withSeconds
     * @return bool
     */
    public function isTime(bool $withSeconds = false): bool
    {
        return $this->date("H:i" . ($withSeconds ? ":s" : ""));
    }

    /**
     * Check "minimum" age (value format should be validated date "Y-m-d")
     * @param int $checkAge
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function isAge(int $checkAge): bool
    {
        $now = (int)$this->dateTime->format("Y");
        $dateTime = new DateTime($this->value);
        $birth = (int)$dateTime->format("Y");
        $age = ($now - $birth);
        return ($age >= $checkAge);
    }

    /**
     * Check if is valid domain
     * @param  bool $strict stricter = true
     * @return bool
     */
    public function isDomain(bool $strict = true): bool
    {
        $strict = ($strict) ? FILTER_FLAG_HOSTNAME : 0;
        return (filter_var((string)$this->value, FILTER_VALIDATE_DOMAIN, $strict) !== false);
    }

    /**
     * Check if is valid URL (http|https is required)
     * @return bool
     */
    public function isUrl(): bool
    {
        return (filter_var($this->value, FILTER_VALIDATE_URL) !== false);
    }

    /**
     * Check if "Host|domain" has an valid DNS (will check A, AAAA and MX)
     * @psalm-suppress UndefinedConstant
     * @noinspection PhpComposerExtensionStubsInspection
     * @return bool
     */
    public function isResolvableHost(): bool
    {
        return $this->dns()->isResolvableHost();
    }

    /**
     * Strict data type validation check if value is a valid HTTP status code
     *
     * @return bool
     */
    public function isHttpStatusCode(): bool
    {
        $validCodes = [
            100, 101, 102, 103,
            200, 201, 202, 203, 204, 205, 206, 207, 208, 226,
            300, 301, 302, 303, 304, 305, 307, 308,
            400, 401, 402, 403, 404, 405, 406, 407, 408, 409,
            410, 411, 412, 413, 414, 415, 416, 417, 418, 421,
            422, 423, 424, 425, 426, 428, 429, 431, 451,
            500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511
        ];
        return in_array((int)$this->value, $validCodes, true);
    }

    /**
     * Strict data type validation check if value is HTTP 200 OK
     *
     * @return bool
     */
    public function isHttp200(): bool
    {
        return (int)$this->value === 200;
    }

    /**
     * Strict data type validation check if value is a 2xx success HTTP code
     *
     * @return bool
     */
    public function isHttpSuccess(): bool
    {
        $intVal = (int)$this->value;
        return $intVal >= 200 && $intVal < 300;
    }


    /**
     * Strict data type validation check if value is a 4xx client error HTTP code
     *
     * @return bool
     */
    public function isHttpClientError(): bool
    {
        $intVal = (int)$this->value;
        return $intVal >= 400 && $intVal < 500;
    }

    /**
     * Strict data type validation check if value is a 5xx server error HTTP code
     *
     * @return bool
     */
    public function isHttpServerError(): bool
    {
        $intVal = (int)$this->value;
        return $intVal >= 500 && $intVal < 600;
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
}

<?php

namespace MaplePHP\Validate;

use BadMethodCallException;
use ErrorException;

/**
 * @method self withValue(mixed $value)
 * @method self eq(string $key, bool $immutable = '1')
 * @method self validateInData(string $key, string $validate, array $args = '')
 * @method self getLength(string $value)
 * @method self luhn()
 * @method self dns()
 * @method self isRequired()
 * @method self isTrue()
 * @method self isTruthy()
 * @method self isFalse()
 * @method self isFalsy()
 * @method self isInArray(array $haystack)
 * @method self isLooselyInArray(array $haystack)
 * @method self keyExists(string|int|float $key)
 * @method self hasValue()
 * @method self isSocialNumber()
 * @method self isOrgNumber()
 * @method self isCreditCard()
 * @method self isVatNumber()
 * @method self isEmail()
 * @method self isDeliverableEmail()
 * @method self contains(string $needle)
 * @method self startsWith(string $needle)
 * @method self endsWith(string $needle)
 * @method self findInString(string $match, ?int $pos = '')
 * @method self isPhone()
 * @method self isZip(int $minLength, ?int $maxLength = '')
 * @method self isFloat()
 * @method self isInt()
 * @method self isString()
 * @method self isArray()
 * @method self isObject()
 * @method self isBool()
 * @method self isResource()
 * @method self isJson()
 * @method self isFullHtml()
 * @method self isBoolVal()
 * @method self isNull()
 * @method self isDir()
 * @method self isFile()
 * @method self isFileOrDirectory()
 * @method self isWritable()
 * @method self isReadable()
 * @method self isNumber()
 * @method self isNumbery()
 * @method self isPositive()
 * @method self isNegative()
 * @method self min(float $int)
 * @method self max(float $int)
 * @method self length(int $min, ?int $max = '')
 * @method self isArrayEmpty()
 * @method self itemsAreTruthy(string|int|float $key)
 * @method self hasTruthyItem(string|int|float $key)
 * @method self isCountEqualTo(int $length)
 * @method self isCountMoreThan(int $length)
 * @method self isCountLessThan(int $length)
 * @method self toIntEqual(int $value)
 * @method self isLengthEqualTo(int $length)
 * @method self isEqualTo(mixed $expected)
 * @method self isLooselyEqualTo(mixed $expected)
 * @method self isNotEqualTo(mixed $value)
 * @method self isLooselyNotEqualTo(mixed $value)
 * @method self isLessThan(int|float $num)
 * @method self isMoreThan(int|float $num)
 * @method self isValidVersion(bool $strict = '')
 * @method self versionCompare(string $withVersion, string $operator = '==')
 * @method self isLossyPassword(int $length = '1')
 * @method self isStrictPassword(int $length = '1')
 * @method self isMatchingPattern(string $charRange)
 * @method self isAlpha()
 * @method self isLowerAlpha()
 * @method self isUpperAlpha()
 * @method self isHex()
 * @method self isDate(string $format = 'Y-m-d')
 * @method self isDateWithTime()
 * @method self isTime(bool $withSeconds = '')
 * @method self isAge(int $checkAge)
 * @method self isDomain(bool $strict = '1')
 * @method self isUrl()
 * @method self isResolvableHost()
 * @method self isHttpStatusCode()
 * @method self isHttp200()
 * @method self isHttpSuccess()
 * @method self isHttpClientError()
 * @method self isHttpServerError()
 * @method self oneOf(array $arr)
 * @method self allOf(array $arr)
 * @method self dateRange(string $format = 'Y-m-d H:i')
 */
class ValidationChain
{
    private mixed $value;
    private ?string $key = null;
    private ?string $validationName = null;
    private array $error = [];

    /**
     * Constructor for the ValidationChain class.
     *
     * @param mixed $value The initial value to be validated.
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
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
     * Magic method to handle dynamic method calls on the object.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return self Returns the current instance of the class.
     * @throws ErrorException
     */
    public function __call(string $name, array $arguments): self
    {
        $this->validateWith($name, $arguments);
        return $this;
    }

    public function mapErrorToKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function mapErrorValidationName(string $key): self
    {
        $this->validationName = $key;
        return $this;
    }

    /**
     * Access a validation from a Validator instance
     *
     * @param string $name
     * @param array $arguments
     * @return bool
     * @throws ErrorException
     */
    public function validateWith(string $name, array $arguments = []): bool
    {
        $invert = str_starts_with($name, "!");
        if ($invert) {
            $name = substr($name, 1);
        }

        $inp = new Validator($this->value);
        if(!method_exists($inp, $name)) {
            throw new BadMethodCallException("Method $name does not exist in class " . Validator::class . ".");
        }
        $valid = $inp->$name(...$arguments);

        // If using the traverse method in Validator
        if($valid instanceof Validator) {
            throw new BadMethodCallException("The method ->$name() is not supported with " .
                __CLASS__ . ". Use ->validateInData() instead!");
        }

        if($invert) {
            $valid = !$valid;
        }

        $name = !is_null($this->validationName) ? $this->validationName : $name;
        if(!is_null($this->key)) {
            $this->error[$this->key][$name] = !$valid;
        } else {
            $this->error[][$name] = !$valid;
        }

        $this->validationName = $this->key = null;
        return $valid;
    }

    /**
     * Retrieves the errors recorded during the validation process.
     *
     * NOTICE: Every error item that has true is true that it found an error!
     *
     * @return array Returns an associative array of errors where the key is the method name
     *               and the value is the arguments passed to the method.
     */
    public function getError(): array
    {
        $this->error = array_map('array_filter', $this->error);
        $this->error = array_filter($this->error);
        return $this->error;
    }

    /**
     * Checks if there are any errors recorded in the validation process.
     *
     * @return bool Returns true if there are validation errors, otherwise false.
     */
    public function hasError(): bool
    {
        return !!$this->getError();
    }

    /**
     * Checks if the current state is valid based on the presence of an error.
     *
     * @return bool Returns true if there is no error, otherwise false.
     */
    public function isValid(): bool
    {
        return !$this->getError();
    }

    /*
    public function getNormalizedError(): array
    {
        $new = [];
        $error = $this->getError();
        foreach($error as $keyA => $arr) {
            foreach($arr as $keyB => $bool) {
                $new[$keyA][$keyB] = !$bool;
            }
        }
        return $new;
    }
     */
}
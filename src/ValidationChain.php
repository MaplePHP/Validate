<?php

namespace MaplePHP\Validate;

use BadMethodCallException;
use ErrorException;
use MaplePHP\DTO\Traverse;
use MaplePHP\Validate\Validators\DNS;
use MaplePHP\Validate\Validators\Luhn;

/**
 * @method self withValue(mixed $value)
 * @method self eq(string $key, bool $immutable = '1')
 * @method self validateInData(string $key, string $validate, array $args = '')
 * @method self getLength(string $value)
 * @method Luhn luhn()
 * @method DNS dns()
 * @method self isRequired()
 * @method self hasResponse()
 * @method self isTrue()
 * @method self isTruthy()
 * @method self isFalse()
 * @method self isFalsy()
 * @method self isInArray(mixed $needle)
 * @method self isLooselyInArray(mixed $needle)
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
 * @method self isLength(int $length)
 * @method self isArrayEmpty()
 * @method self hasValueAt(string|int|float $key, mixed $needle)
 * @method self hasJsonValueAt(string|int|float $key, mixed $needle)
 * @method self itemsAreTruthy(string|int|float $key)
 * @method self hasTruthyItem(string|int|float $key)
 * @method self isCountEqualTo(int $length)
 * @method self isCountMoreThan(int $length)
 * @method self isCountLessThan(int $length)
 * @method self toIntEqual(int $value)
 * @method self isLengthEqualTo(int $length)
 * @method self isEqualTo(mixed $expected)
 * @method self isInstanceOf(object|string $instance)
 * @method self isClass(string $instance)
 * @method self isLooselyEqualTo(mixed $expected)
 * @method self isNotEqualTo(mixed $value)
 * @method self isLooselyNotEqualTo(mixed $value)
 * @method self isLessThan(int|float $num)
 * @method self isGreaterThan(int|float $num)
 * @method self isValidVersion(bool $strict = '')
 * @method self versionCompare(string $withVersion, string $operator = '==')
 * @method self isLossyPassword(int $length = '1')
 * @method self isStrictPassword(int $length = '1')
 * @method self isMatchingPattern(string $charRange)
 * @method self isAlpha()
 * @method self isLowerAlpha()
 * @method self isUpperAlpha()
 * @method self isHexColor()
 * @method self isHexString(?int $length = null)
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
 * @method self isRequestMethod()
 * @method self hasKey(string|int|float $key)
 * @method self hasQueryParam(string $queryParamKey, ?string $queryParamValue = null)
 * @method self oneOf(array $arr)
 * @method self allOf(array $arr)
 * @method self dateRange(string $format = 'Y-m-d H:i')
 * @method self notWithValue(mixed $value)
 * @method self notEq(string $key, bool $immutable = '1')
 * @method self notValidateInData(string $key, string $validate, array $args = '')
 * @method self notGetLength(string $value)
 * @method Luhn notLuhn()
 * @method DNS notDns()
 * @method self notIsRequired()
 * @method self notHasResponse()
 * @method self notIsTrue()
 * @method self notIsTruthy()
 * @method self notIsFalse()
 * @method self notIsFalsy()
 * @method self notIsInArray(array $haystack)
 * @method self notIsLooselyInArray(array $haystack)
 * @method self notKeyExists(string|int|float $key)
 * @method self notHasValue()
 * @method self notIsSocialNumber()
 * @method self notIsOrgNumber()
 * @method self notIsCreditCard()
 * @method self notIsVatNumber()
 * @method self notIsEmail()
 * @method self notIsDeliverableEmail()
 * @method self notContains(string $needle)
 * @method self notStartsWith(string $needle)
 * @method self notEndsWith(string $needle)
 * @method self notFindInString(string $match, ?int $pos = '')
 * @method self notIsPhone()
 * @method self notIsZip(int $minLength, ?int $maxLength = '')
 * @method self notIsFloat()
 * @method self notIsInt()
 * @method self notIsString()
 * @method self notIsArray()
 * @method self notIsObject()
 * @method self notIsBool()
 * @method self notIsResource()
 * @method self notIsJson()
 * @method self notIsFullHtml()
 * @method self notIsBoolVal()
 * @method self notIsNull()
 * @method self notIsDir()
 * @method self notIsFile()
 * @method self notIsFileOrDirectory()
 * @method self notIsWritable()
 * @method self notIsReadable()
 * @method self notIsNumber()
 * @method self notIsNumbery()
 * @method self notIsPositive()
 * @method self notIsNegative()
 * @method self notMin(float $int)
 * @method self notMax(float $int)
 * @method self notLength(int $min, ?int $max = '')
 * @method self notIsLength(int $length)
 * @method self notIsArrayEmpty()
 * @method self notHasValueAt(string|int|float $key, mixed $needle)
 * @method self notHasJsonValueAt(string|int|float $key, mixed $needle)
 * @method self notItemsAreTruthy(string|int|float $key)
 * @method self notHasTruthyItem(string|int|float $key)
 * @method self notIsCountEqualTo(int $length)
 * @method self notIsCountMoreThan(int $length)
 * @method self notIsCountLessThan(int $length)
 * @method self notToIntEqual(int $value)
 * @method self notIsLengthEqualTo(int $length)
 * @method self notIsEqualTo(mixed $expected)
 * @method self notIsInstanceOf(object|string $instance)
 * @method self notIsClass(string $instance)
 * @method self notIsLooselyEqualTo(mixed $expected)
 * @method self notIsNotEqualTo(mixed $value)
 * @method self notIsLooselyNotEqualTo(mixed $value)
 * @method self notIsLessThan(int|float $num)
 * @method self notIsGreaterThan(int|float $num)
 * @method self notIsValidVersion(bool $strict = '')
 * @method self notVersionCompare(string $withVersion, string $operator = '==')
 * @method self notIsLossyPassword(int $length = '1')
 * @method self notIsStrictPassword(int $length = '1')
 * @method self notIsMatchingPattern(string $charRange)
 * @method self notIsAlpha()
 * @method self notIsLowerAlpha()
 * @method self notIsUpperAlpha()
 * @method self notIsHexColor()
 * @method self notIsHexString(?int $length = null)
 * @method self notIsDate(string $format = 'Y-m-d')
 * @method self notIsDateWithTime()
 * @method self notIsTime(bool $withSeconds = '')
 * @method self notIsAge(int $checkAge)
 * @method self notIsDomain(bool $strict = '1')
 * @method self notIsUrl()
 * @method self notIsResolvableHost()
 * @method self notIsHttpStatusCode()
 * @method self notIsHttp200()
 * @method self notIsHttpSuccess()
 * @method self notIsHttpClientError()
 * @method self notIsHttpServerError()
 * @method self notIsRequestMethod()
 * @method self notHasKey(string|int|float $key)
 * @method self notHasQueryParam(string $queryParamKey, ?string $queryParamValue = null)
 * @method self notOneOf(array $arr)
 * @method self notAllOf(array $arr)
 * @method self notDateRange(string $format = 'Y-m-d H:i')
 * @method self isThrowable(callable $compare)
 */
class ValidationChain
{
    private mixed $value;
    private ?string $key = null;
    private ?string $validationName = null;
    private array $error = [];
    private array $errorArgs = [];

    /**
     * Constructor for the ValidationChain class.
     *
     * @param mixed $value The initial value to be validated.
     */
    public function __construct(mixed $value)
    {
        $this->setValue($value);
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

    // Alias to getValue
    public function get(): mixed
    {
        return $this->getValue();
    }

    // Alias to getValue
    public function val(): mixed
    {
        return $this->getValue();
    }

    /**
     * Set a value
     *
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
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
        $newName = Traverse::value($name)
            ->strCamelCaseToArr()
            ->shift($rest)
            ->implode()
            ->toString();
        if ($rest === "not") {
            $name = "!" . $newName;
        }
        $this->validateWith($name, $arguments);
        return $this;
    }

    /**
     * You can add a name to error keys
     *
     * @param string|null $key
     * @return $this
     */
    public function mapErrorToKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * You can overwrite the expected validation name on error
     *
     * @param string|null $key
     * @return $this
     */
    public function mapErrorValidationName(?string $key): self
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
    public function validateWith(string $name, array $arguments): bool
    {
        $invert = str_starts_with($name, "!");
        if ($invert) {
            $name = substr($name, 1);
        }

        $inp = new Validator($this->value);
        if (!method_exists($inp, $name)) {
            throw new BadMethodCallException("Method $name does not exist in class " . Validator::class . ".");
        }
        /*
         if (isset($arguments[0][0])) {
            $arguments = Traverse::value($arguments)->flatten()->toArray();
        }
         */
        $valid = $inp->$name(...$arguments);

        // If using the traverse method in Validator
        if ($valid instanceof Validator) {
            throw new BadMethodCallException("The method ->$name() is not supported with " .
                __CLASS__ . ". Use ->validateInData() instead!");
        }

        if ($invert) {
            $valid = !$valid;
        }

        $name = $this->validationName !== null ? $this->validationName : $name;
        $name = ($invert) ? "not" . ucfirst($name) : $name;

        if ($this->key !== null) {
            $this->error[$this->key][$name] = !$valid ? $arguments : false;
        } else {
            $this->error[][$name] = !$valid ? $arguments : false;
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
    public function getFailedValidations(): array
    {

        $this->error = array_map(fn($item) => array_filter($item, fn($v) => $v !== false), $this->error);
        $this->error = array_filter($this->error);
        return $this->error;
    }

    public function getFailedArguments(): array
    {
        $this->errorArgs = array_map('array_filter', $this->errorArgs);
        $this->errorArgs = array_filter($this->errorArgs);
        return $this->errorArgs;
    }

    // Alias for "getFailedValidations" (used in Unitary)
    public function getError(): array
    {
        return $this->getFailedValidations();
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

    /**
     * Add validation description
     *
     * @param string|null $description
     * @return ValidationChain
     */
    public function describe(?string $description = null): self
    {
        if (!$this->isValid()) {
            $this->error[][$description] = null;
        };
        return $this;
    }

    /**
     * Add validation description (Alias to @describe)
     *
     * @param string|null $description
     * @return void
     */
    public function assert(?string $description = null): void
    {
        assert($this->isValid(), $description);
    }

    /**
     * Add validation description (Alias to @describe)
     *
     * @param string|null $description
     * @return void
     */
    public function validate(?string $description = null): void
    {
        $this->describe($description);
    }
}

<?php

namespace MaplePHP\Validate;

/**
 * @method self withValue(mixed $value)
 * @method self value(mixed $value)
 * @method self getLength(string $value)
 * @method self luhn()
 * @method self required()
 * @method self hasValue()
 * @method self socialNumber()
 * @method self personalNumber()
 * @method self orgNumber()
 * @method self creditCard()
 * @method self vatNumber()
 * @method self email()
 * @method self findInString(string $match, ?int $pos)
 * @method self phone()
 * @method self zip(int $arg1, ?int $arg2)
 * @method self isFloat()
 * @method self isInt()
 * @method self isString()
 * @method self isStr()
 * @method self isArray()
 * @method self isObject()
 * @method self isBool()
 * @method self isJson()
 * @method self isFullHtml()
 * @method self isBoolVal()
 * @method self isNull()
 * @method self isFile()
 * @method self isDir()
 * @method self isResource()
 * @method self isWritable()
 * @method self isReadable()
 * @method self number()
 * @method self numeric()
 * @method self numericVal()
 * @method self positive()
 * @method self negative()
 * @method self min(float $int)
 * @method self minAlt(float $int)
 * @method self max(float $int)
 * @method self length(int $arg1, ?int $arg2)
 * @method self equalLength(int $arg1)
 * @method self equal($str)
 * @method self lessThan($num)
 * @method self moreThan($num)
 * @method self contains(string $needle)
 * @method self startsWith(string $needle)
 * @method self endsWith(string $needle)
 * @method self notEqual($str)
 * @method self validVersion(bool $strict)
 * @method self versionCompare(string $withVersion, string $operator)
 * @method self lossyPassword(int $length)
 * @method self strictPassword(int $length)
 * @method self pregMatch($matchStr)
 * @method self atoZ()
 * @method self lowerAtoZ()
 * @method self upperAtoZ()
 * @method self hex()
 * @method self date(string $format)
 * @method self dateTime(string $format)
 * @method self time(string $format)
 * @method self dateRange(string $format)
 * @method self age(int $arg1)
 * @method self domain(bool $strict)
 * @method self url()
 * @method self dns()
 * @method self matchDNS(int $type)
 * @method self oneOf(array $arr)
 * @method self allOf(array $arr)
 */
class ValidatePool
{
    private mixed $value;
    private array $error = [];

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Magic method to handle dynamic method calls on the object.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return self Returns the current instance of the class.
     * @throws \ErrorException
     */
    public function __call(string $name, array $arguments): self
    {
        $inp = new Inp($this->value);
        if(!method_exists($inp, $name)) {
            throw new \BadMethodCallException("Method $name does not exist in class " . Inp::class . ".");
        }
        $this->error[$name] = !$inp->$name(...$arguments);
        return $this;
    }

    /**
     * Retrieves the errors recorded during the validation process.
     *
     * @return array Returns an associative array of errors where the key is the method name
     *               and the value is the arguments passed to the method.
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * Checks if there are any errors recorded in the validation process.
     *
     * @return bool Returns true if there are validation errors, otherwise false.
     */
    public function hasError(): bool
    {
        $this->error = array_filter($this->error);
        return !!$this->error;
    }

    /**
     * Checks if the current state is valid based on the presence of an error.
     *
     * @return bool Returns true if there is no error, otherwise false.
     */
    public function isValid(): bool
    {
        $this->error = array_filter($this->error);
        return !$this->error;
    }
}
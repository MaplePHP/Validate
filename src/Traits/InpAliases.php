<?php
/**
 * @Package:    MaplePHP - Validate - Aliases
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
 * Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Validate\Traits;

trait InpAliases {

    public function isDns(): bool
    {
        return $this->isResolvableHost();
    }

    public function url(): bool
    {
        return $this->isUrl();
    }

    public function domain(bool $strict = true): bool
    {
        return $this->isDomain($strict);
    }

    public function age(int $checkAge): bool
    {
        return $this->isAge($checkAge);
    }

    public function time(): bool
    {
        return $this->isTime();
    }

    public function dateTime(): bool
    {
        return $this->dateTime();
    }

    public function date(string $format = "Y-m-d"): bool
    {
        return $this->isDate();
    }

    public function hex(): bool
    {
        return $this->isHex();
    }

    public function strictPassword(int $length = 1): bool
    {
        return $this->isStrictPassword($length);
    }

    public function lossyPassword(int $length = 1): bool
    {
        return $this->isLossyPassword($length);
    }

    public function validVersion(bool $strict = false): bool
    {
        return $this->isValidVersion();
    }

    public function moreThan(float|int $num): bool
    {
        return $this->isMoreThan($num);
    }

    public function lessThan(float|int $num): bool
    {
        return $this->isLessThan($num);
    }

    public function notEqual(mixed $value): bool
    {
        return $this->isNotEqualTo($value);
    }

    public function equal(mixed $value): bool
    {
        return $this->isEqualTo($value);
    }

    public function equalLength(int $length): bool
    {
        return $this->isLengthEqualTo($length);
    }

    public function negative(): bool
    {
        return $this->isNegative();
    }

    public function positive(): bool
    {
        return $this->isPositive();
    }

    public function number(): bool
    {
        return $this->isNumber();
    }

    public function zip(int $minLength, ?int $maxLength = null): bool
    {
        return $this->isZip($minLength, $maxLength);
    }

    public function phone(): bool
    {
        return $this->isPhone();
    }

    public function email(): bool
    {
        return $this->isEmail();
    }

    public function vatNumber(): bool
    {
        return $this->isVatNumber();
    }

    public function creditCard(): bool
    {
        return $this->isCreditCard();
    }

    public function orgNumber(): bool
    {
        return $this->isOrgNumber();
    }

    public function socialNumber(): bool
    {
        return $this->isSocialNumber();
    }

    public function personalNumber(): bool
    {
        return $this->socialNumber();
    }

    public function required(): bool
    {
        return $this->isRequired();
    }

    public function isStr(): bool
    {
        return $this->isString();
    }

    public function minimum(float $int): bool
    {
        return $this->min($int);
    }

    public function maximum(float $int): bool
    {
        return $this->max($int);
    }

    public function pregMatch(string $match): bool
    {
        return $this->isMatchingPattern($match);
    }

    public function atoZ(): bool
    {
        return $this->isAlpha();
    }

    public function lowerAtoZ(): bool
    {
        return $this->isLowerAlpha();
    }

    public function upperAtoZ(): bool
    {
        return $this->isUpperAlpha();
    }
}
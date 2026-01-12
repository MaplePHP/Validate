<?php

/**
 * @Package:    MaplePHP - Validate vat number
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, it's part of the license.
 */

namespace MaplePHP\Validate\Validators;

class Vat
{
    /**
     * Regular expression per country code
     * @var array<string, string>
     * @link http://ec.europa.eu/taxation_customs/vies/faq.html?locale=lt#item_11
     */
    public const PATTERNS = [
        'AT' => 'U[A-Z\d]{8}',
        'BE' => '(0\d{9}|\d{10})',
        'BG' => '\d{9,10}',
        'CY' => '\d{8}[A-Z]',
        'CZ' => '\d{8,10}',
        'DE' => '\d{9}',
        'DK' => '(\d{2} ?){3}\d{2}',
        'EE' => '\d{9}',
        'EL' => '\d{9}',
        'ES' => '([A-Z]\d{7}[A-Z]|\d{8}[A-Z]|[A-Z]\d{8})',
        'FI' => '\d{8}',
        'FR' => '[A-Z\d]{2}\d{9}',
        'GB' => '(\d{9}|\d{12}|(GD|HA)\d{3})',
        'HR' => '\d{11}',
        'HU' => '\d{8}',
        'IE' => '([A-Z\d]{8}|[A-Z\d]{9})',
        'IT' => '\d{11}',
        'LT' => '(\d{9}|\d{12})',
        'LU' => '\d{8}',
        'LV' => '\d{11}',
        'MT' => '\d{8}',
        'NL' => '\d{9}B\d{2}',
        'PL' => '\d{10}',
        'PT' => '\d{9}',
        'RO' => '\d{2,10}',
        'SE' => '\d{12}',
        'SI' => '\d{8}',
        'SK' => '\d{10}'
    ];

    private string $country;
    private string $number;

    public function __construct(string $vatNumber)
    {
        $this->country = substr($vatNumber, 0, 2);
        $this->number = substr($vatNumber, 2);
    }

    /**
     * Validate a VAT country and if is EU.
     * @return bool
     */
    public function validateCountry(): bool
    {
        return (isset($this::PATTERNS[$this->country]));
    }

    /**
     * Get the selected country code
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->country;
    }

    /**
     * Validate a VAT number format. This does not check whether the VAT number was really issued.
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->validateCountry()) {
            /** @var array<string, string> $pattern */
            $pattern = $this::PATTERNS;
            return (preg_match('/^' . $pattern[$this->country] . '$/', $this->number) > 0);
        }
        return false;
    }
}

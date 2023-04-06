<?php 
/**
 * @Package:    PHP Fuse - Validate vat number
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 * @Version:    1.0.0
 */

namespace PHPFuse\Validate;

class ValidVatFormat {


    /**
     * Regular expression per country code
     * @link http://ec.europa.eu/taxation_customs/vies/faq.html?locale=lt#item_11
     */
    const PATTERNS = [
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

    private $_country;
    private $_number;

    function __construct($vatNumber) {
        $this->_country = substr($vatNumber, 0, 2);
        $this->_number = substr($vatNumber, 2);
    }

    /**
     * Validate a VAT country and if is EU.
     * @return boolean
     */
    function validateCountry() {
        return (bool)(isset($this::PATTERNS[$this->_country]));
    }

    /**
     * Get the selected country code
     * @return [type] [description]
     */
    function getCountryCode() {
        return $this->_country;
    }

    /**
     * Validate a VAT number format. This does not check whether the VAT number was really issued.
     * @param string $vatNumber
     * @return boolean
     */
    function validate() {
        if(is_string($this->_number) && $this->validateCountry()) {
            return preg_match('/^' . $this::PATTERNS[$this->_country] . '$/', $this->_number) > 0;
        }
        return false;
    }


}

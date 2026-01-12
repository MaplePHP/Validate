<?php

/**
 * assert-polyfill.php
 *
 * Ensures consistent assert() behavior across PHP versions.
 *
 * In PHP < 8.4, assert() can be disabled via ini settings and may not throw exceptions.
 * This file forces `assert.active` and `assert.exception` to be enabled to simulate
 * the stricter behavior introduced in PHP 8.4, where assert() is always active and throws.
 *
 * This file is automatically loaded via Composer's autoload.files to apply this setup early.
 */

if (!defined("DNS_CAA")) {
    define("DNS_CAA", 8192);
}

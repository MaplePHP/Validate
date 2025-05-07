<?php

/**
 * @Package:    MaplePHP - Input validation interface
 * @Author:     Daniel Ronkainen
 * @Licence:    Apache-2.0 license, Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace MaplePHP\Validate\Interfaces;

interface InpInterface
{
    public static function value(string $value): self;
}

<?php

/**
 * @Package:    PHP Fuse - Input validation interface
 * @Author:     Daniel Ronkainen
 * @Licence:    The MIT License (MIT), Copyright © Daniel Ronkainen
                Don't delete this comment, its part of the license.
 */

namespace PHPFuse\Validate\Interfaces;

interface InpInterface
{
    public static function value($value): self;
}

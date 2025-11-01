<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoConstructorExceptionChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;
use RuntimeException;

final class ThrowingConstructor implements WithoutMethods
{
    public function __construct()
    {
        throw new RuntimeException('fail');
    }
}

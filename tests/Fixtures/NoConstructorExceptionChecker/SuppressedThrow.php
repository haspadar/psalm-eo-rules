<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoConstructorExceptionChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;
use RuntimeException;

final class SuppressedThrow implements WithoutMethods
{
    public function __construct()
    {
        /** @psalm-suppress NoConstructorException */
        throw new RuntimeException('ignored');
    }
}

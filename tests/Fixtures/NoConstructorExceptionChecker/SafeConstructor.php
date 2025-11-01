<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoConstructorExceptionChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

/**
 * @psalm-suppress UnusedVariable
 */
final class SafeConstructor implements WithoutMethods
{
    public function __construct()
    {
        $a = 1;
    }
}

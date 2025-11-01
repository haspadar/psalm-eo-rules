<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNewInsideMethodChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;
use RuntimeException;

final class ThrowingException implements WithoutMethods
{
    public function ok(): void
    {
        throw new RuntimeException('fail');
    }
}

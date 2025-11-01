<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullLiteralChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class WithNull implements WithoutMethods
{
    public function val(): mixed
    {
        return null;
    }
}

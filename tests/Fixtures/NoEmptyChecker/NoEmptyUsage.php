<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoEmptyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class NoEmptyUsage implements WithoutMethods
{
    public function ok(): bool
    {
        $x = 1;
        return $x > 0;
    }
}

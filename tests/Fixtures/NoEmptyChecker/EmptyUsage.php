<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoEmptyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class EmptyUsage implements WithoutMethods
{
    public function fail(): bool
    {
        $x = [];
        return empty($x);
    }
}

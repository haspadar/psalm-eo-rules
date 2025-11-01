<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoStaticPropertyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class StaticPropertyUsage implements WithoutMethods
{
    private readonly int $id;

    public function __construct()
    {
        $this->id = 0;
    }

    public function run(): void
    {
        self::$id;
    }
}

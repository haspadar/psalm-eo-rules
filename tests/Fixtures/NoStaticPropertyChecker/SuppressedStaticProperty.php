<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoStaticPropertyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedStaticProperty implements WithoutMethods
{
    /**
     * @psalm-suppress NoMutableProperty
     * @psalm-suppress NoStaticProperty
     */
    private static int $count;

    public function __construct()
    {
        /** @psalm-suppress NoStaticProperty */
        self::$count = 1;
    }
}

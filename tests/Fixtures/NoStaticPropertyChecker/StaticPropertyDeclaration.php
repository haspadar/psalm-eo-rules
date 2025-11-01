<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoStaticPropertyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class StaticPropertyDeclaration implements WithoutMethods
{
    public static int $count = 0;
}

<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoStaticMethodDeclarationChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedStaticMethod implements WithoutMethods
{
    /** @psalm-suppress NoStaticMethodDeclaration */
    public static function ok(): void
    {
    }
}

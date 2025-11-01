<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoStaticMethodDeclarationChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class NonStaticMethod implements WithoutMethods
{
    public function ok(): void
    {
    }
}

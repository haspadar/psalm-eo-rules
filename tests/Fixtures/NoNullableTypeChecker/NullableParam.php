<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullableTypeChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class NullableParam implements WithoutMethods
{
    public function bad(?string $name): void
    {
    }
}

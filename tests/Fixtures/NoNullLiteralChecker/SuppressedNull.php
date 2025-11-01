<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullLiteralChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedNull implements WithoutMethods
{
    /** @psalm-suppress NoNullLiteral */
    public function val(): mixed
    {
        return null;
    }
}

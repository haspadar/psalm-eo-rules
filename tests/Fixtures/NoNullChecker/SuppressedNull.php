<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedNull implements WithoutMethods
{
    /** @psalm-suppress NoNull */
    public function val(): mixed
    {
        return null;
    }
}

<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNewInsideMethodChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedNew implements WithoutMethods
{
    /** @psalm-suppress NoNewInsideMethod */
    public function ok(): object
    {
        return new \stdClass(); // suppressed
    }
}

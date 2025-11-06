<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

/**
 * Simple test case where null is passed into an internal PHP function.
 */
function demo(string $str): string
{
    return mb_substr($str, 1, null, 'UTF-8');
}

final class WithInternalFunction implements WithoutMethods
{
    public function process(string $str): int|false
    {
        return mb_strpos($str, 'e', 1, null);
    }
}

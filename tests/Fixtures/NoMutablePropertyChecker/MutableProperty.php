<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoMutablePropertyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class MutableProperty implements WithoutMethods
{
    public int $id;

    public function __construct()
    {
        $this->id = 1;
    }
}

<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNewInsideMethodChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

abstract class Base implements WithoutMethods
{
    public function __construct()
    {
    }
    public function run(): void
    {
    }
}

final class ParentCtor extends Base
{
    public function __construct()
    {
        parent::__construct();
    }
}

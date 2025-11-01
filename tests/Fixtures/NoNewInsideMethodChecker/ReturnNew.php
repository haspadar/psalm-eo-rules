<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNewInsideMethodChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;
use stdClass;

final class ReturnNew implements WithoutMethods
{
    private ?object $value = null;

    public function ok(): object
    {
        $this->value = new stdClass();
        return $this->value;
    }
}

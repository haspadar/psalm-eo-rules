<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoNullChecker;

function acceptsString(?string $str): void
{
}

final class WithNullArgument
{
    public function demo(): void
    {
        acceptsString(null);
    }
}

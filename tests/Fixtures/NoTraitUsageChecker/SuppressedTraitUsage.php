<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoTraitUsageChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

trait Shared
{
}

/** @psalm-suppress NoTraitUsage */
final class SuppressedTraitUsage implements WithoutMethods
{
    use Shared;
}

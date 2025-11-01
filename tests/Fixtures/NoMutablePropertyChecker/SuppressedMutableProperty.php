<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoMutablePropertyChecker;

/** @psalm-suppress NoInterfaceImplementation */
final class SuppressedMutableProperty
{
    /** @psalm-suppress NoMutableProperty */
    public int $id;

    public function __construct()
    {
        $this->id = 1;
    }
}

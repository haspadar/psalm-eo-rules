<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules\Issue;

use Psalm\Issue\CodeIssue;

/**
 * Reported when the `new` operator is used inside a method or constructor.
 */
final class NoNewInsideMethodIssue extends CodeIssue
{
}

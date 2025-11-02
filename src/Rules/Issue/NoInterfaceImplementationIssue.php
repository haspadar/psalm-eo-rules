<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules\Issue;

use Psalm\Issue\CodeIssue;

/**
 * Reported when a concrete class does not implement any interface.
 *
 * EO rule: every concrete class must implement at least one interface
 * to preserve polymorphism and explicit contracts.
 */
final class NoInterfaceImplementationIssue extends CodeIssue
{
}

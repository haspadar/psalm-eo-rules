<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class NoMutablePropertyCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Passes when properties are readonly')]
    public function readonlyPropertyPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoMutablePropertyChecker/ReadonlyProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error for mutable properties')]
    public function mutablePropertyTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoMutablePropertyChecker/MutableProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for mutable properties')]
    public function suppressedMutablePropertyIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoMutablePropertyChecker/SuppressedMutableProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

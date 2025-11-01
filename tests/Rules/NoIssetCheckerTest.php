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

final class NoIssetCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Passes when no isset() is used')]
    public function classWithoutIssetPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoIssetChecker/NoIsset.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error when isset() is used')]
    public function issetUsageTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoIssetChecker/WithIsset.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for isset() usage')]
    public function suppressedIssetIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoIssetChecker/SuppressedIsset.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

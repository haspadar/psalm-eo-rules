<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoNullChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoNullChecker::class)]
final class NoNullCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Honors suppression for null literals')]
    public function suppressedNullIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/SuppressedNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Allows null passed to internal PHP functions')]
    public function allowsNullInInternalFunction(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithInternalFunction.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error when standalone null literal is used')]
    public function reportsErrorOnBareNull(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Passes when no null literal is present')]
    public function passesWithoutNull(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithoutNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports error when null is returned from a method')]
    public function reportsErrorOnNullReturn(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithNullReturn.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Reports error when null is assigned to a variable')]
    public function reportsErrorOnNullAssignment(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithNullAssignment.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Reports error when null is passed as a function argument')]
    public function reportsErrorOnNullArgument(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullChecker/WithNullArgument.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }
}

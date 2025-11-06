<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoConstructorExceptionChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoConstructorExceptionChecker::class)]
final class NoConstructorExceptionCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Passes when constructor is missing')]
    public function classWithoutConstructorPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoConstructorExceptionChecker/NoConstructor.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Passes when constructor does not throw')]
    public function constructorWithoutThrowPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoConstructorExceptionChecker/SafeConstructor.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error when constructor throws')]
    public function constructorWithThrowTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoConstructorExceptionChecker/ThrowingConstructor.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for throwing constructors')]
    public function suppressedThrowIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoConstructorExceptionChecker/SuppressedThrow.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Ignores throw inside a closure within constructor')]
    public function throwInsideClosureIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoConstructorExceptionChecker/ClosureThrow.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

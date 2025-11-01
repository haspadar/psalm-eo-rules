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

/**
 * @covers \Haspadar\PsalmEoRules\Rules\NoNewInsideMethodChecker
 */
final class NoNewInsideMethodCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports an error when new is assigned inside a method')]
    public function assignNewInMethodTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNewInsideMethodChecker/NewInMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Reports an error when new is returned from a method')]
    public function returnNewTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNewInsideMethodChecker/ReturnNew.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Allows throwing new exceptions')]
    public function throwingExceptionIsAllowed(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNewInsideMethodChecker/ThrowingException.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Allows delegating to parent constructor')]
    public function parentConstructorIsAllowed(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNewInsideMethodChecker/ParentCtor.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Honors suppression for new expressions')]
    public function suppressedNewIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNewInsideMethodChecker/SuppressedNew.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

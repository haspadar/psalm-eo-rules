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

final class NonFinalOrAbstractClassCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Passes when class is final')]
    public function finalClassPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NonFinalOrAbstractClassChecker/FinalClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Passes when class is abstract')]
    public function abstractClassPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NonFinalOrAbstractClassChecker/AbstractClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error for concrete non final classes')]
    public function concreteNonFinalClassTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NonFinalOrAbstractClassChecker/ConcreteClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for non final classes')]
    public function suppressedViolationIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NonFinalOrAbstractClassChecker/SuppressedConcreteClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

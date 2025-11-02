<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoInterfaceImplementationChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoInterfaceImplementationChecker::class)]
final class NoInterfaceImplementationCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Allows abstract classes without interfaces')]
    public function abstractClassIsAllowed(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoInterfaceImplementationChecker/AbstractClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Passes when a class implements an interface')]
    public function classImplementingInterfacePasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoInterfaceImplementationChecker/ImplementsInterface.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error for concrete classes without interfaces')]
    public function concreteClassWithoutInterfaceTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoInterfaceImplementationChecker/NoInterface.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for missing interface implementations')]
    public function suppressedViolationIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoInterfaceImplementationChecker/SuppressedNoInterface.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

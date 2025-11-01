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

final class NoStaticMethodDeclarationCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports an error when a static method is declared')]
    public function staticMethodTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticMethodDeclarationChecker/StaticMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Allows non static methods without errors')]
    public function nonStaticMethodPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticMethodDeclarationChecker/NonStaticMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Honors @psalm-suppress NoStaticMethodDeclaration directives')]
    public function suppressedStaticMethodIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticMethodDeclarationChecker/SuppressedStaticMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

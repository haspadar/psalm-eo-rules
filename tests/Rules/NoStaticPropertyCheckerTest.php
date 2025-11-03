<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoStaticPropertyChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoStaticPropertyChecker::class)]
final class NoStaticPropertyCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports error on static property declaration')]
    public function staticPropertyDeclarationTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticPropertyChecker/StaticPropertyDeclaration.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Reports error on static property usage')]
    public function staticPropertyUsageTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticPropertyChecker/StaticPropertyUsage.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Passes for instance properties')]
    public function instancePropertyPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticPropertyChecker/InstanceProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Ignores @psalm-suppress NoStaticProperty')]
    public function suppressedStaticPropertyIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoStaticPropertyChecker/SuppressedStaticProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

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

final class NoEmptyCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Passes when the file does not use empty()')]
    public function fileWithoutEmptyPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoEmptyChecker/NoEmptyUsage.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error when empty() is used')]
    public function fileWithEmptyTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoEmptyChecker/EmptyUsage.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for empty() usage')]
    public function suppressedEmptyIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoEmptyChecker/SuppressedEmpty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Reports an error on empty() inside functions')]
    public function emptyInsideFunctionStillTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoEmptyChecker/EmptyInFunction.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }
}

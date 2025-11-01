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
 * @covers \Haspadar\PsalmEoRules\Rules\NoNullLiteralChecker
 */
final class NoNullLiteralCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports an error when null literal is used')]
    public function nullLiteralTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullLiteralChecker/WithNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Honors suppression for null literals')]
    public function suppressedNullIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullLiteralChecker/SuppressedNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Passes when no null literal is present')]
    public function noNullLiteralPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullLiteralChecker/WithoutNull.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

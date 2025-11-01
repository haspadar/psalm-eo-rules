<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoNullableTypeChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoNullableTypeChecker::class)]
final class NoNullableTypeCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports an error for nullable parameters')]
    public function nullableParameterTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullableTypeChecker/NullableParam.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Passes when parameters are non nullable')]
    public function nonNullableParameterPasses(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullableTypeChecker/NonNullableParam.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Honors suppression on nullable methods')]
    public function suppressedMethodIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullableTypeChecker/SuppressedMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Honors suppression on nullable classes')]
    public function suppressedClassIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoNullableTypeChecker/SuppressedClass.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

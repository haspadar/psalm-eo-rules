<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoProtectedChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoProtectedChecker::class)]
final class NoProtectedCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Reports an error when a property is protected')]
    public function protectedPropertyTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoProtectedChecker/ProtectedProperty.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Reports an error when a method is protected')]
    public function protectedMethodTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoProtectedChecker/ProtectedMethod.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Allows private and public members')]
    public function privateAndPublicMembersPass(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoProtectedChecker/PrivateAndPublicMembers.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }

    #[Test]
    #[TestDox('Honors @psalm-suppress NoProtected directives')]
    public function suppressedProtectedIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoProtectedChecker/SuppressedProtected.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Rules;

use Haspadar\PsalmEoRules\Rules\NoTraitUsageChecker;
use Haspadar\PsalmEoRules\Tests\Constraint\PsalmAnalysisConstraint;
use Haspadar\PsalmEoRules\Tests\PsalmRunner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoTraitUsageChecker::class)]
final class NoTraitUsageCheckerTest extends TestCase
{
    private PsalmRunner $runner;

    protected function setUp(): void
    {
        $this->runner = new PsalmRunner();
    }

    #[Test]
    #[TestDox('Detects trait usage and reports violation')]
    public function classWithTraitTriggersError(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoTraitUsageChecker/ClassWithTrait.php');
        self::assertThat($result, new PsalmAnalysisConstraint(true));
    }

    #[Test]
    #[TestDox('Ignores @psalm-suppress NoTraitUsage')]
    public function suppressedTraitUsageIsIgnored(): void
    {
        $result = $this->runner->analyze(__DIR__ . '/../Fixtures/NoTraitUsageChecker/SuppressedTraitUsage.php');
        self::assertThat($result, new PsalmAnalysisConstraint(false));
    }
}

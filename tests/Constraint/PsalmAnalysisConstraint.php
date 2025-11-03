<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

final class PsalmAnalysisConstraint extends Constraint
{
    public function __construct(
        private readonly bool $shouldHaveError,
    ) {
    }

    public function toString(): string
    {
        return $this->shouldHaveError
            ? 'to contain at least one Psalm ERROR'
            : 'to contain no Psalm errors';
    }

    /**
     * @param array{exitCode:int,output:string} $other
     */
    protected function matches($other): bool
    {
        $hasError = str_contains($other['output'], 'ERROR') || $other['exitCode'] !== 0;
        return $this->shouldHaveError === $hasError;
    }

    /**
     * @param array{exitCode:int,output:string} $other
     */
    protected function failureDescription($other): string
    {
        $summary = sprintf(
            "Psalm exited with code %d.\n--- Output ---\n%s\n--------------",
            $other['exitCode'],
            trim($other['output'])
        );

        return $this->shouldHaveError
            ? "Expected Psalm to report errors, but it did not.\n$summary"
            : "Expected Psalm to pass cleanly, but it reported errors.\n$summary";
    }
}

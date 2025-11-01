<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoProtectedChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class SuppressedProtected implements WithoutMethods
{
    /** @psalm-suppress NoProtected */
    protected readonly int $id;

    /** @psalm-suppress NoProtected */
    protected function hidden(): void
    {
    }
}

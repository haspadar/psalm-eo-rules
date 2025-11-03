<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoProtectedChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

final class PrivateAndPublicMembers implements WithoutMethods
{
    private readonly string $privateProperty;
    public readonly string $publicProperty;

    private function privateMethod(): void
    {
    }

    public function publicMethod(): void
    {
    }
}

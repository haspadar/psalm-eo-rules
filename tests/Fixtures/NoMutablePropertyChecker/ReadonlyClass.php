<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests\Fixtures\NoMutablePropertyChecker;

use Haspadar\PsalmEoRules\Tests\Fixtures\WithoutMethods;

/** readonly */
final readonly class ReadonlyClass implements WithoutMethods
{
    private int $a;

    public function __construct(int $a)
    {
        $this->a = $a;
    }
}

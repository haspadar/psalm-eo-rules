<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoTraitUsageIssue;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterCodebasePopulatedInterface;
use Psalm\Plugin\EventHandler\Event\AfterCodebasePopulatedEvent;

/**
 * Detects usage of traits in classes.
 *
 * EO rule: traits violate encapsulation and blur object boundaries.
 * Prefer composition or delegation instead of code inclusion.
 */
final class NoTraitUsageChecker implements AfterCodebasePopulatedInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoTraitUsage';

    /**
     * Reports an issue for each detected trait usage that is not suppressed.
     *
     * @param AfterCodebasePopulatedEvent $event Psalm event containing the analyzed codebase
     */
    public static function afterCodebasePopulated(AfterCodebasePopulatedEvent $event): void
    {
        $provider = $event->getCodebase()->classlike_storage_provider;

        /** @psalm-suppress InternalClass, InternalMethod */
        foreach ($provider->getAll() as $class) {
            foreach ($class->used_traits as $trait => $_) {
                $loc = $class->location;

                $violation =
                    $loc instanceof CodeLocation
                    && !in_array(self::SUPPRESS, $class->suppressed_issues, true);

                if ($violation) {
                    IssueBuffer::accepts(
                        new NoTraitUsageIssue(
                            sprintf(
                                'Use of trait %s violates EO rules. Prefer composition or delegation.',
                                $trait
                            ),
                            $loc
                        )
                    );
                }
            }
        }
    }
}

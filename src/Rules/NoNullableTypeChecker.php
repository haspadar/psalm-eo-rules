<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoNullableTypeIssue;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterCodebasePopulatedInterface;
use Psalm\Plugin\EventHandler\Event\AfterCodebasePopulatedEvent;

/**
 * Detects nullable parameter types.
 *
 * EO rule: nullable types violate explicitness and lead to optional behavior.
 * Use `Optional` or `NullObject` abstractions instead of `?Type`.
 */
final class NoNullableTypeChecker implements AfterCodebasePopulatedInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoNullableType';

    /**
     * Reports an issue when a parameter type is declared as nullable.
     *
     * @param AfterCodebasePopulatedEvent $event Psalm event providing codebase context
     */
    #[\Override]
    public static function afterCodebasePopulated(AfterCodebasePopulatedEvent $event): void
    {
        $codebase = $event->getCodebase();
        $classProvider = $codebase->classlike_storage_provider;

        /** @psalm-suppress InternalClass, InternalMethod */
        foreach ($classProvider::getAll() as $class) {
            $classSuppressed = in_array(self::SUPPRESS, $class->suppressed_issues, true);

            foreach ($class->methods as $method) {
                $methodSuppressed =
                    $classSuppressed
                    || in_array(self::SUPPRESS, $method->suppressed_issues, true);

                foreach ($method->params as $param) {
                    $type = $param->type;
                    $loc  = $param->location ?? $method->location;

                    $violation =
                        !$methodSuppressed
                        && $type !== null
                        && $type->isNullable()
                        && $loc instanceof CodeLocation;

                    if ($violation) {
                        IssueBuffer::accepts(
                            new NoNullableTypeIssue(
                                sprintf(
                                    'Nullable type "%s" violates EO rules. Use Optional or NullObject.',
                                    $type->getId()
                                ),
                                $loc
                            )
                        );
                    }
                }
            }
        }
    }
}

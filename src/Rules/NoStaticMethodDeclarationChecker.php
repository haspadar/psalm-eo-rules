<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoStaticMethodDeclarationIssue;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;

/**
 * Detects static method declarations.
 *
 * EO rule: static methods break encapsulation and state consistency.
 * Use object composition or dependency injection instead.
 */
final class NoStaticMethodDeclarationChecker implements AfterFunctionLikeAnalysisInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoStaticMethodDeclaration';

    /**
     * Reports an issue if a static method is declared and not suppressed.
     *
     * @param AfterFunctionLikeAnalysisEvent $event Psalm event containing method storage
     * @return bool|null Always returns null as required by Psalm hook contract
     */
    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        $storage = $event->getFunctionLikeStorage();
        $suppressed = in_array(self::SUPPRESS, $storage->suppressed_issues, true);

        /** @psalm-suppress UndefinedPropertyFetch, InternalProperty */
        $isStatic = property_exists($storage, 'is_static') && $storage->is_static;

        if (
            !$suppressed
            && $isStatic
            && $storage->location instanceof CodeLocation
        ) {
            IssueBuffer::accepts(
                new NoStaticMethodDeclarationIssue(
                    'Static method declarations violate EO rules.',
                    $storage->location
                )
            );
        }

        return null;
    }
}

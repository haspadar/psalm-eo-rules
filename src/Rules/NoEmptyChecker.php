<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoEmptyIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Expr\Empty_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

/**
 * Detects usage of the `empty()` construct.
 *
 * EO rule: `empty()` hides intent and weakens type safety.
 * Use explicit comparison or a NullObject instead.
 */
final class NoEmptyChecker implements AfterExpressionAnalysisInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoEmpty';

    /**
     * Reports an issue when an `empty()` construct is used and not suppressed.
     *
     * @param AfterExpressionAnalysisEvent $event Psalm event containing analyzed expression
     */
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        $isSuppressed = in_array(
            self::SUPPRESS,
            $event->getStatementsSource()->getSuppressedIssues(),
            true
        );

        $violation =
            $expr instanceof Empty_
            && !$isSuppressed;

        if ($violation) {
            IssueBuffer::accepts(
                new NoEmptyIssue(
                    'Use of empty() violates EO rules. Prefer explicit comparison or NullObject.',
                    new CodeLocation($event->getStatementsSource(), $expr)
                )
            );
        }

        return null;
    }
}

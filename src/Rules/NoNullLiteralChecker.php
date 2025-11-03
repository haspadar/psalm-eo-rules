<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoNullIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Expr\ConstFetch;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

/**
 * Detects usage of the `null` literal.
 *
 * EO rule: `null` represents absence and breaks object integrity.
 * Prefer explicit `Optional` or `NullObject` types instead.
 */
final class NoNullLiteralChecker implements AfterExpressionAnalysisInterface
{
    private const SUPPRESS = 'NoNullLiteral';

    #[\Override]
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        if (
            !$expr instanceof ConstFetch
            || strtolower($expr->name->toString()) !== 'null'
        ) {
            return null;
        }

        // Check suppression both in source and AST node (consistent with other checkers)
        $sourceSuppressions = $event->getStatementsSource()->getSuppressedIssues();
        $isSuppressed =
            in_array(self::SUPPRESS, $sourceSuppressions, true)
            || Suppression::has($expr, self::SUPPRESS);

        if ($isSuppressed) {
            return null;
        }

        IssueBuffer::accepts(
            new NoNullIssue(
                'Use of null violates EO rules. Prefer NullObject or explicit optional value.',
                new CodeLocation($event->getStatementsSource(), $expr)
            )
        );

        return null;
    }
}

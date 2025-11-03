<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoStaticPropertyIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Stmt\Property;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\AfterStatementAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\AfterStatementAnalysisEvent;

/**
 * Detects static property declarations and usage.
 *
 * EO rule: static state breaks encapsulation and shared state consistency.
 * Objects must manage data through instance properties only.
 */
final class NoStaticPropertyChecker implements AfterStatementAnalysisInterface, AfterExpressionAnalysisInterface
{
    private const SUPPRESS = 'NoStaticProperty';

    #[\Override]
    public static function afterStatementAnalysis(AfterStatementAnalysisEvent $event): ?bool
    {
        $stmt = $event->getStmt();
        if (!$stmt instanceof Property || !$stmt->isStatic()) {
            return null;
        }

        $suppressed = $event->getStatementsSource()->getSuppressedIssues();
        if (self::isSuppressed($suppressed) || Suppression::has($stmt, self::SUPPRESS)) {
            return null;
        }

        IssueBuffer::accepts(
            new NoStaticPropertyIssue(
                'Static property declarations violate EO rules.',
                new CodeLocation($event->getStatementsSource(), $stmt)
            ),
            $suppressed
        );

        return null;
    }

    #[\Override]
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();
        if (!$expr instanceof StaticPropertyFetch) {
            return null;
        }

        $suppressed = $event->getStatementsSource()->getSuppressedIssues();
        if (self::isSuppressed($suppressed) || Suppression::has($expr, self::SUPPRESS)) {
            return null;
        }

        IssueBuffer::accepts(
            new NoStaticPropertyIssue(
                'Static property usage violates EO rules.',
                new CodeLocation($event->getStatementsSource(), $expr)
            ),
            $suppressed
        );

        return null;
    }

    /**
     * @param list<string> $suppressed
     */
    private static function isSuppressed(array $suppressed): bool
    {
        return in_array(self::SUPPRESS, $suppressed, true)
            || in_array(NoStaticPropertyIssue::class, $suppressed, true)
            || in_array('NoStaticPropertyIssue', $suppressed, true);
    }
}
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
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoStaticProperty';

    /**
     * Reports an issue when a static property is declared and not suppressed.
     *
     * @param AfterStatementAnalysisEvent $event Psalm event containing the analyzed statement
     * @return bool|null Always returns null as required by Psalm hook contract
     */
    public static function afterStatementAnalysis(AfterStatementAnalysisEvent $event): ?bool
    {
        $stmt = $event->getStmt();

        $violation =
            $stmt instanceof Property
            && $stmt->isStatic()
            && !Suppression::has($stmt, self::SUPPRESS);

        if ($violation) {
            IssueBuffer::accepts(
                new NoStaticPropertyIssue(
                    'Static property declarations violate EO rules.',
                    new CodeLocation($event->getStatementsSource(), $stmt)
                )
            );
        }

        return null;
    }

    /**
     * Reports an issue when a static property is accessed and not suppressed.
     *
     * @param AfterExpressionAnalysisEvent $event Psalm event containing analyzed expression
     * @return bool|null Always returns null as required by Psalm hook contract
     */
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        $violation =
            $expr instanceof StaticPropertyFetch
            && !Suppression::has($expr, self::SUPPRESS);

        if ($violation) {
            IssueBuffer::accepts(
                new NoStaticPropertyIssue(
                    'Static property usage violates EO rules.',
                    new CodeLocation($event->getStatementsSource(), $expr)
                )
            );
        }

        return null;
    }
}

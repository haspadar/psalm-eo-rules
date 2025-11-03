<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoIssetIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Expr\Isset_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

/**
 * Detects usage of the `isset()` construct.
 *
 * EO rule: `isset()` obscures intent and weakens type guarantees.
 * Use explicit comparison or a NullObject instead.
 */
final class NoIssetChecker implements AfterExpressionAnalysisInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoIsset';

    /**
     * Reports an issue when an `isset()` call is detected and not suppressed.
     *
     * @param AfterExpressionAnalysisEvent $event Psalm event containing analyzed expression
     * @return bool|null Always returns null as required by Psalm hook contract
     */
    #[\Override]
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        $violation =
            $expr instanceof Isset_
            && !Suppression::has($expr, self::SUPPRESS);

        if ($violation) {
            IssueBuffer::accepts(
                new NoIssetIssue(
                    'Use of isset() violates EO rules. Prefer explicit comparison or NullObject.',
                    new CodeLocation($event->getStatementsSource(), $expr)
                )
            );
        }

        return null;
    }
}

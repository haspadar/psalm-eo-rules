<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoNullIssue;
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

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        if (!$expr instanceof ConstFetch || strtolower($expr->name->toString()) !== 'null') {
            return null;
        }

        // Psalm tracks suppression in StatementsSource, not AST
        $source = $event->getStatementsSource();
        $filePath = $source->getFilePath();
        $suppressions = $source->getSuppressedIssues();

        // direct match (active suppressions in this file)
        if (in_array(self::SUPPRESS, $suppressions, true)) {
            return null;
        }

        // additional fallback: look for suppression in raw file text
        $contents = file_get_contents($filePath);
        if ($contents !== false && str_contains($contents, '@psalm-suppress ' . self::SUPPRESS)) {
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

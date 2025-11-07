<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoConstructorException;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Throw_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

final class NoConstructorExceptionChecker implements AfterExpressionAnalysisInterface
{
    private const SUPPRESS = 'NoConstructorException';

    #[\Override]
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        if (!$expr instanceof Throw_) {
            return null;
        }

        if (!self::isInConstructor($event)) {
            return null;
        }

        if (self::isSuppressed($event, $expr)) {
            return null;
        }

        if (self::isInsideClosure($event, $expr)) {
            return null;
        }

        IssueBuffer::accepts(
            new NoConstructorException(
                'Throwing exceptions inside constructors violates EO rules. '
                . 'Use a factory or validation object instead.',
                new CodeLocation($event->getStatementsSource(), $expr)
            )
        );

        return null;
    }

    private static function isInConstructor(AfterExpressionAnalysisEvent $event): bool
    {
        $methodId = $event->getContext()->calling_method_id;
        return $methodId !== null && str_ends_with($methodId, '::__construct');
    }

    private static function isSuppressed(AfterExpressionAnalysisEvent $event, Throw_ $expr): bool
    {
        $sourceSuppressions = $event->getStatementsSource()->getSuppressedIssues();
        return in_array(self::SUPPRESS, $sourceSuppressions, true)
            || Suppression::has($expr, self::SUPPRESS);
    }

    private static function isInsideClosure(AfterExpressionAnalysisEvent $event, Throw_ $expr): bool
    {
        $source = $event->getStatementsSource();
        $stmts  = $source->getCodebase()->getStatementsForFile($source->getFilePath());
        if (!is_array($stmts)) {
            return false;
        }

        $throwStart = $expr->getStartFilePos();
        $throwEnd   = $expr->getEndFilePos();
        $found      = false;

        $walk = static function (array $nodes) use (&$walk, $throwStart, $throwEnd, &$found): void {
            foreach ($nodes as $node) {
                if ($node instanceof ArrowFunction || $node instanceof Closure) {
                    $fnStart = $node->getStartFilePos();
                    $fnEnd   = $node->getEndFilePos();

                    if ($throwStart >= $fnStart && $throwEnd <= $fnEnd) {
                        $found = true;
                        return;
                    }
                }

                foreach (get_object_vars($node) as $prop) {
                    if (is_array($prop)) {
                        $walk($prop);
                    } elseif ($prop instanceof Node) {
                        $walk([$prop]);
                    }
                }

                if ($found) {
                    return;
                }
            }
        };

        $walk($stmts);

        return $found;
    }



}

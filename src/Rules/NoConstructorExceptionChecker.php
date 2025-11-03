<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoConstructorException;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Expr\Throw_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;

/**
 * Detects `throw` expressions inside class constructors.
 *
 * EO rule: constructors must not throw exceptions.
 * Use factories or validation objects for error handling instead.
 */
final class NoConstructorExceptionChecker implements AfterExpressionAnalysisInterface
{
    private const SUPPRESS = 'NoConstructorException';

    #[\Override]
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        $expr = $event->getExpr();

        // Обрабатываем только выражения throw
        if (!$expr instanceof Throw_) {
            return null;
        }

        // Определяем, что мы находимся внутри конструктора
        $context   = $event->getContext();
        $method_id = $context->calling_method_id;
        if ($method_id === null || !str_ends_with($method_id, '::__construct')) {
            return null;
        }

        // Проверка подавления (через аннотацию @psalm-suppress)
        $sourceSuppressions = $event->getStatementsSource()->getSuppressedIssues();
        $isSuppressed =
            in_array(self::SUPPRESS, $sourceSuppressions, true)
            || Suppression::has($expr, self::SUPPRESS);

        if ($isSuppressed) {
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
}

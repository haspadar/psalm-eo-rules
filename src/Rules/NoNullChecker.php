<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoNullIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\Return_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterFunctionCallAnalysisInterface;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\AfterStatementAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionCallAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Psalm\Plugin\EventHandler\Event\AfterStatementAnalysisEvent;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Detects `null` usage violating EO rules.
 *
 * EO rule: `null` represents absence and breaks object integrity.
 * Prefer explicit `Optional` or `NullObject` types instead.
 *
 * Handles:
 *  - return null;
 *  - $x = null;
 *  - passing null into user-defined functions or methods;
 * Skips: internal PHP functions or methods.
 */
final class NoNullChecker implements
    AfterFunctionCallAnalysisInterface,
    AfterMethodCallAnalysisInterface,
    AfterStatementAnalysisInterface
{
    private const SUPPRESS = 'NoNull';

    #[\Override]
    public static function afterStatementAnalysis(AfterStatementAnalysisEvent $event): ?bool
    {
        $stmt = $event->getStmt();

        // detect `return null`
        if (
            $stmt instanceof Return_
            && $stmt->expr instanceof ConstFetch
            && strtolower($stmt->expr->name->toString()) === 'null'
        ) {
            self::report($event, $stmt->expr, 'return');
        }

        // detect `$x = null`
        if (
            $stmt instanceof Node\Stmt\Expression
            && $stmt->expr instanceof Assign
            && $stmt->expr->expr instanceof ConstFetch
            && strtolower($stmt->expr->expr->name->toString()) === 'null'
        ) {
            self::report($event, $stmt->expr->expr, 'assignment');
        }

        return null;
    }

    #[\Override]
    public static function afterFunctionCallAnalysis(AfterFunctionCallAnalysisEvent $event): void
    {
        $expr = $event->getExpr();

        if ($expr->name instanceof Node\Name) {
            $funcName = $expr->name->toString();

            if (self::isInternalFunction($funcName)) {
                return; // skip internal PHP functions like mb_substr()
            }
        }

        self::checkArgs($expr->args, $event, 'function');
    }

    #[\Override]
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        [$class, $method] = explode('::', $event->getMethodId());

        if (self::isInternalMethod($class, $method)) {
            return;
        }

        self::checkArgs($event->getExpr()->args, $event, 'method');
    }

    /**
     * @param array<Node\Arg|Node\VariadicPlaceholder> $args
     */
    private static function checkArgs(
        array $args,
        AfterFunctionCallAnalysisEvent|AfterMethodCallAnalysisEvent|AfterStatementAnalysisEvent $event,
        string $context
    ): void {
        foreach ($args as $arg) {
            if (!$arg instanceof Node\Arg) {
                continue;
            }

            $value = $arg->value;

            if (
                $value instanceof ConstFetch
                && strtolower($value->name->toString()) === 'null'
            ) {
                $sourceSuppressions = $event->getStatementsSource()->getSuppressedIssues();
                $isSuppressed =
                    in_array(self::SUPPRESS, $sourceSuppressions, true)
                    || Suppression::has($value, self::SUPPRESS);

                if ($isSuppressed) {
                    continue;
                }

                IssueBuffer::accepts(
                    new NoNullIssue(
                        sprintf('[%s] Use of null violates EO rules. Prefer NullObject or explicit optional value.', $context),
                        new CodeLocation($event->getStatementsSource(), $value)
                    )
                );
            }
        }
    }

    private static function report(
        AfterFunctionCallAnalysisEvent|AfterMethodCallAnalysisEvent|AfterStatementAnalysisEvent $event,
        ConstFetch $expr,
        string $context
    ): void {
        $sourceSuppressions = $event->getStatementsSource()->getSuppressedIssues();
        $isSuppressed =
            in_array(self::SUPPRESS, $sourceSuppressions, true)
            || Suppression::has($expr, self::SUPPRESS);

        if ($isSuppressed) {
            return;
        }

        IssueBuffer::accepts(
            new NoNullIssue(
                sprintf('[%s] Use of null violates EO rules. Prefer NullObject or explicit optional value.', $context),
                new CodeLocation($event->getStatementsSource(), $expr)
            )
        );
    }

    private static function isInternalFunction(string $name): bool
    {
        try {
            return (new ReflectionFunction($name))->isInternal();
        } catch (ReflectionException) {
            return false;
        }
    }

    private static function isInternalMethod(string $class, string $method): bool
    {
        if (!class_exists($class) && !interface_exists($class)) {
            return false;
        }

        try {
            return (new ReflectionMethod($class, $method))->isInternal();
        } catch (ReflectionException) {
            return false;
        }
    }
}

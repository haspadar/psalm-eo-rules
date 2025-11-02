<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoConstructorExceptionIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Throw_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects `throw` expressions inside class constructors.
 *
 * EO rule: constructors must not throw exceptions.
 * Use factories or validation objects for error handling instead.
 */
final class NoConstructorExceptionChecker implements AfterClassLikeVisitInterface
{
    private const SUPPRESS = 'NoConstructorException';

    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $class = $event->getStmt();
        if (!$class instanceof Class_) {
            return;
        }

        // skip entire class if suppression exists
        if (Suppression::has($class, self::SUPPRESS)) {
            return;
        }

        foreach ($class->stmts as $stmt) {
            if (!$stmt instanceof Node\Stmt\ClassMethod) {
                continue;
            }

            if (strtolower($stmt->name->toString()) !== '__construct') {
                continue;
            }

            // skip constructor if suppressed
            if (Suppression::has($stmt, self::SUPPRESS)) {
                continue;
            }

            foreach ($stmt->stmts ?? [] as $subStmt) {
                self::inspect($subStmt, $event, false);
            }
        }
    }

    /**
     * Recursively inspects AST nodes for `throw` expressions inside constructors.
     */
    private static function inspect(Node $node, AfterClassLikeVisitEvent $event, bool $parentSuppressed): void
    {
        $suppressed = $parentSuppressed || Suppression::has($node, self::SUPPRESS);

        if ($suppressed) {
            return;
        }

        if ($node instanceof Throw_) {
            IssueBuffer::accepts(
                new NoConstructorExceptionIssue(
                    'Throwing exceptions inside constructors violates EO rules. '
                    . 'Use a factory or validation object instead.',
                    new CodeLocation($event->getStatementsSource(), $node)
                )
            );
        }

        foreach ($node->getSubNodeNames() as $name) {
            $child = $node->$name ?? null;

            if ($child instanceof Node) {
                self::inspect($child, $event, $suppressed);
            } elseif (is_array($child)) {
                foreach ($child as $sub) {
                    if ($sub instanceof Node) {
                        self::inspect($sub, $event, $suppressed);
                    }
                }
            }
        }
    }
}

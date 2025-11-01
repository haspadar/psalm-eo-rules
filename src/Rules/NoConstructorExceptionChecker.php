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
    /**
     * Suppression code used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoConstructorException';

    /**
     * Scans all class constructors for `throw` expressions.
     *
     * @param AfterClassLikeVisitEvent $event Psalm event containing class node and source
     */
    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $class = $event->getStmt();
        if (!$class instanceof Class_) {
            return;
        }

        foreach ($class->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\ClassMethod
                && strtolower($stmt->name->toString()) === '__construct') {
                foreach ($stmt->stmts ?? [] as $subStmt) {
                    self::inspect($subStmt, $event);
                }
            }
        }
    }

    /**
     * Recursively inspects AST nodes for `throw` expressions inside constructors.
     *
     * @param Node                     $node  Node to analyze
     * @param AfterClassLikeVisitEvent $event Psalm event providing context
     */
    private static function inspect(Node $node, AfterClassLikeVisitEvent $event): void
    {
        if ($node instanceof Throw_ && !Suppression::has($node, self::SUPPRESS)) {
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
                self::inspect($child, $event);
            } elseif (is_array($child)) {
                foreach ($child as $sub) {
                    if ($sub instanceof Node) {
                        self::inspect($sub, $event);
                    }
                }
            }
        }
    }
}

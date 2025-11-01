<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoNewInsideMethodIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;
use Throwable;

/**
 * Detects object creation (`new`) inside methods and constructors.
 *
 * EO rule: object construction must be delegated outside methods.
 * Allowed cases:
 *  - `parent::__construct()`
 *  - `new` creating a Throwable
 *  - `new` in return expressions (delegation)
 *  - `new` assigned to a local variable (not a property)
 */
final class NoNewInsideMethodChecker implements AfterClassLikeVisitInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoNewInsideMethod';

    /**
     * Scans all class methods and constructors for invalid `new` expressions.
     *
     * @param AfterClassLikeVisitEvent $event Psalm event containing class node and source
     */
    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $class = $event->getStmt();
        if (!$class instanceof Class_) {
            return;
        }

        foreach ($class->getMethods() as $method) {
            foreach ($method->stmts ?? [] as $stmt) {
                self::scan($stmt, $event, $method);
            }
        }
    }

    /**
     * Recursively traverses AST and reports disallowed `new` expressions.
     *
     * @param Node                     $node   Node being analyzed
     * @param AfterClassLikeVisitEvent $event  Psalm event providing context
     * @param ClassMethod              $method Current method under inspection
     */
    private static function scan(Node $node, AfterClassLikeVisitEvent $event, ClassMethod $method): void
    {
        if ($node instanceof New_ && !Suppression::has($node, self::SUPPRESS)) {
            $parent = $node->getAttribute('parent');
            $fqcn = $node->class instanceof Node\Name ? $node->class->toString() : null;

            $isThrowable = $fqcn !== null
                && class_exists($fqcn)
                && is_a($fqcn, Throwable::class, true);

            $isParentCtor = false;
            if ($parent instanceof Node\Arg) {
                $call = $parent->getAttribute('parent');
                $isParentCtor =
                    $call instanceof StaticCall
                    && $call->name instanceof Node\Identifier
                    && $call->class instanceof Node\Name
                    && strtolower($call->name->toString()) === '__construct'
                    && strtolower($call->class->toString()) === 'parent';
            }

            $isReturnValue = $parent instanceof Node\Stmt\Return_;
            $isLocalAssign =
                ($parent instanceof Assign
                    && $parent->var instanceof Node\Expr\Variable
                    && is_string($parent->var->name))
                || self::isAssignedLocally($node, array_values($method->stmts ?? []));

            if (
                !$isThrowable
                && !$isParentCtor
                && !$isReturnValue
                && !$isLocalAssign
            ) {
                IssueBuffer::accepts(
                    new NoNewInsideMethodIssue(
                        sprintf(
                            'Object creation in %s() violates EO rules. '
                            . 'Allowed only for exceptions, delegation, parent constructor calls, or local variables.',
                            $method->name
                        ),
                        new CodeLocation($event->getStatementsSource(), $node)
                    )
                );
            }
        }

        foreach ($node->getSubNodeNames() as $name) {
            foreach ((array)($node->$name ?? []) as $child) {
                if ($child instanceof Node) {
                    $child->setAttribute('parent', $node);
                    self::scan($child, $event, $method);
                }
            }
        }
    }

    /**
     * Determines if a `new` expression is assigned to a local variable.
     *
     * @param New_            $node   The `new` expression node
     * @param list<Node\Stmt> $stmts  Method statements for local context
     * @return bool True if assigned locally, false otherwise
     */
    private static function isAssignedLocally(New_ $node, array $stmts): bool
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\Expression && $stmt->expr instanceof Assign) {
                $assign = $stmt->expr;
                if (
                    $assign->expr === $node &&
                    $assign->var instanceof Node\Expr\Variable &&
                    is_string($assign->var->name)
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}

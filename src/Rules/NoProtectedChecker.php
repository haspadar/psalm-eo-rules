<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoProtectedIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects protected methods and properties.
 *
 * EO rule: inheritance is prohibited; only `private` or `public` members are allowed.
 * Protected visibility hides ownership and breaks clear object boundaries.
 */
final class NoProtectedChecker implements AfterClassLikeVisitInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NoProtected';

    /**
     * Reports an issue for any protected method or property declaration.
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
            if ($stmt instanceof Property && $stmt->isProtected() && !Suppression::has($stmt, self::SUPPRESS)) {
                IssueBuffer::accepts(
                    new NoProtectedIssue(
                        'Protected properties are forbidden. Use private for encapsulation.',
                        new CodeLocation($event->getStatementsSource(), $stmt)
                    )
                );
            }

            if ($stmt instanceof ClassMethod && $stmt->isProtected() && !Suppression::has($stmt, self::SUPPRESS)) {
                IssueBuffer::accepts(
                    new NoProtectedIssue(
                        'Protected methods are forbidden. Use private or public instead.',
                        new CodeLocation($event->getStatementsSource(), $stmt)
                    )
                );
            }
        }
    }
}

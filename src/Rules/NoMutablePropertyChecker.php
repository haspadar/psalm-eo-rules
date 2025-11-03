<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoMutablePropertyIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects mutable (non-readonly) class and promoted properties.
 *
 * EO rule: state must be immutable. Properties should be marked readonly.
 */
final class NoMutablePropertyChecker implements AfterClassLikeVisitInterface
{
    private const SUPPRESS = 'NoMutableProperty';

    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $class = $event->getStmt();
        if (!$class instanceof Class_) {
            return;
        }

        // Regular properties
        foreach ($class->stmts as $stmt) {
            if (!$stmt instanceof Property) {
                continue;
            }

            if ($stmt->isReadonly()) {
                continue;
            }

            if (Suppression::has($stmt, self::SUPPRESS)) {
                continue;
            }

            IssueBuffer::accepts(
                new NoMutablePropertyIssue(
                    'Properties must be declared readonly.',
                    new CodeLocation($event->getStatementsSource(), $stmt),
                ),
            );
        }

        // Promoted properties in constructors
        foreach ($class->stmts as $stmt) {
            if (!$stmt instanceof ClassMethod) {
                continue;
            }

            foreach ($stmt->params as $param) {
                if (
                    ($param->flags & (Class_::MODIFIER_PUBLIC | Class_::MODIFIER_PROTECTED | Class_::MODIFIER_PRIVATE)) === 0
                ) {
                    continue;
                }

                if (($param->flags & Class_::MODIFIER_READONLY) !== 0) {
                    continue;
                }

                if (Suppression::has($param, self::SUPPRESS)) {
                    continue;
                }

                IssueBuffer::accepts(
                    new NoMutablePropertyIssue(
                        'Properties must be declared readonly.',
                        new CodeLocation($event->getStatementsSource(), $param),
                    ),
                );
            }
        }
    }
}

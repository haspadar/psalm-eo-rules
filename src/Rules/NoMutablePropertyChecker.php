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
use PhpParser\Node\Stmt\Property;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects mutable (non-readonly) class properties.
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
                    new CodeLocation($event->getStatementsSource(), $stmt)
                )
            );
        }
    }
}

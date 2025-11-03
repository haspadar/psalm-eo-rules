<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NonFinalOrAbstractClassIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects classes that are neither final nor abstract.
 *
 * EO rule: every class must be explicitly closed for inheritance (`final`)
 * or intentionally designed for extension (`abstract`).
 */
final class NonFinalOrAbstractClassChecker implements AfterClassLikeVisitInterface
{
    /**
     * Suppression code name used in `@psalm-suppress`.
     *
     * @var string
     */
    private const SUPPRESS = 'NonFinalOrAbstractClass';

    /**
     * Reports an issue if a class is concrete but not marked as final or abstract.
     *
     * @param AfterClassLikeVisitEvent $event Psalm event containing class node and source
     */
    #[\Override]
    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $node = $event->getStmt();

        $violation =
            $node instanceof Class_
            && $node->name instanceof Identifier
            && !Suppression::has($node, self::SUPPRESS)
            && !$node->isFinal()
            && !$node->isAbstract();

        if ($violation) {
            IssueBuffer::accepts(
                new NonFinalOrAbstractClassIssue(
                    'Classes must be either final or abstract.',
                    new CodeLocation($event->getStatementsSource(), $node)
                )
            );
        }
    }
}

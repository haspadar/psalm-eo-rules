<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Rules;

use Haspadar\PsalmEoRules\Rules\Issue\NoInterfaceImplementationIssue;
use Haspadar\PsalmEoRules\Suppression;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;

/**
 * Detects concrete classes that neither extend nor implement anything.
 *
 * EO rule: every concrete class must extend a base type or implement an interface
 * to remain substitutable and integrated into the object graph.
 */
final class NoInterfaceImplementationChecker implements AfterClassLikeVisitInterface
{
    private const SUPPRESS = 'NoInterfaceImplementation';

    public static function afterClassLikeVisit(AfterClassLikeVisitEvent $event): void
    {
        $node = $event->getStmt();

        if (
            !$node instanceof Class_
            || !$node->name instanceof Identifier
            || $node->isAbstract()
            || Suppression::has($node, self::SUPPRESS)
        ) {
            return;
        }

        // Нормально, если есть extends или implements
        $hasExtends = $node->extends !== null;
        $hasImplements = $node->implements !== [];

        if ($hasExtends || $hasImplements) {
            return;
        }

        IssueBuffer::accepts(
            new NoInterfaceImplementationIssue(
                'Each concrete class must either extend a base class or implement an interface.',
                new CodeLocation($event->getStatementsSource(), $node)
            )
        );
    }
}

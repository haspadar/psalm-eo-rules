<?php

/*
 * SPDX-FileCopyrightText: Copyright (c) 2025 Kanstantsin Mesnik
 * SPDX-License-Identifier: MIT
 */
declare(strict_types=1);

namespace Haspadar\PsalmEoRules;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

/**
 * Plugin entry point for Psalm EO Rules.
 *
 * Registers all Elegant Objects rule checkers in Psalm.
 * Each checker enforces a specific design constraint
 * such as forbidding inheritance, nulls, or static usage.
 *
 * @psalm-suppress UnusedClass
 */
final class Plugin implements PluginEntryPointInterface
{
    /**
     * Fully-qualified class names of all rule checkers.
     *
     * @var list<class-string>
     */
    private const RULES = [
        Rules\NoNullLiteralChecker::class,
        Rules\NoNullableTypeChecker::class,
        Rules\NoIssetChecker::class,
        Rules\NoEmptyChecker::class,
        Rules\NoStaticMethodDeclarationChecker::class,
        Rules\NoStaticPropertyChecker::class,
        Rules\NoMutablePropertyChecker::class,
        Rules\NonFinalOrAbstractClassChecker::class,
        Rules\NoInterfaceImplementationChecker::class,
        Rules\NoTraitUsageChecker::class,
        Rules\NoConstructorExceptionChecker::class,
        Rules\NoNewInsideMethodChecker::class,
        Rules\NoProtectedChecker::class,
    ];

    /**
     * Registers all rule checkers in Psalm.
     *
     * @param RegistrationInterface $registration Psalm plugin registration object
     * @param SimpleXMLElement|null $config       Optional plugin configuration
     */
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }

        foreach (self::RULES as $rule) {
            if (!class_exists($rule)) {
                continue;
            }

            $registration->registerHooksFromClass($rule);
        }
    }
}

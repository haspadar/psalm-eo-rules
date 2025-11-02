<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules;

use PhpParser\Comment\Doc;
use PhpParser\Node;

/**
 * Utility class that detects `@psalm-suppress` directives in a node's docblock.
 */
final class Suppression
{
    /**
     * Determines whether the given node has a suppression annotation
     * for the specified issue code.
     *
     * @param Node   $node       AST node to inspect
     * @param string $issueCode  Issue code to search for
     *
     * @return bool True if the suppression directive is present
     */
    public static function has(Node $node, string $issueCode): bool
    {
        $doc = $node->getDocComment();
        if (!$doc instanceof Doc) {
            return false;
        }

        $text = $doc->getText();
        $pattern = '/@psalm-suppress\s+' . preg_quote($issueCode, '/') . '(?:\s|$)/';

        return (bool) preg_match($pattern, $text);
    }
}

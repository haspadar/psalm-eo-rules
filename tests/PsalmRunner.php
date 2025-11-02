<?php

declare(strict_types=1);

namespace Haspadar\PsalmEoRules\Tests;

use RuntimeException;
use Symfony\Component\Process\Process;

final class PsalmRunner
{
    public function __construct(
        private readonly string $binary = 'vendor/bin/psalm',
    ) {
    }

    public function analyze(string $path, array $extraArgs = []): array
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Path not found: $path");
        }

        $config = getcwd() . '/psalm-test.xml';
        if (!file_exists($config)) {
            throw new RuntimeException("Config not found: $config");
        }

        $args = [
            $this->binary,
            '--no-cache',
            '--no-progress',
            '--config=' . $config,
        ];

        if ($extraArgs !== []) {
            $args = array_merge($args, $extraArgs);
        }

        $args[] = $path;

        $process = new Process($args, getcwd());
        $process->run();

        return [
            'exitCode' => $process->getExitCode(),
            'output' => $process->getOutput() . $process->getErrorOutput(),
        ];
    }

    public function hasError(string $output): bool
    {
        return str_contains($output, 'ERROR');
    }
}

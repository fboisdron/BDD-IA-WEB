<?php

declare(strict_types=1);

final class PythonBridge
{
    public function run(string $scriptPath, array $arguments): array
    {
        if (!is_file($scriptPath)) {
            return ['ok' => false, 'error' => 'Script Python introuvable.'];
        }

        $parts = [PYTHON_BIN, escapeshellarg($scriptPath)];
        foreach ($arguments as $name => $value) {
            $parts[] = '--' . $name;
            $parts[] = escapeshellarg((string) $value);
        }

        $command = implode(' ', $parts) . ' 2>&1';
        $output = [];
        $code = 0;
        exec($command, $output, $code);
        $text = trim(implode("\n", $output));

        return [
            'ok' => $code === 0,
            'code' => $code,
            'output' => $text,
        ];
    }

    public function parseAge(string $output)
    {
        if (preg_match('/Âge estimé\s*:\s*([0-9]+(?:\.[0-9]+)?)/u', $output, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    public function parseCluster(string $output)
    {
        if (preg_match('/Catégorie\s*:\s*(.+)$/m', $output, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public function parseAlert(string $output): array
    {
        $probability = null;
        if (preg_match('/Probabilité estimée de risque\s*:\s*([0-9]+(?:\.[0-9]+)?)/u', $output, $matches)) {
            $probability = (float) $matches[1];
        }

        return [
            'alert' => preg_match('/^ALERTE\b/mu', $output) === 1,
            'probability' => $probability,
        ];
    }
}

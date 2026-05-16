<?php

declare(strict_types=1);

namespace App\Services;

class LlmScrubberService
{
    private const REDACTED = '[REDACTED]';

    private const PATTERNS = [
        // Email
        '/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/' => '[EMAIL]',
        // Téléphone (formats africains et internationaux)
        '/(?:\+?[\d\s\-\.]{7,15}|\b0\d{8,9}\b)/' => '[PHONE]',
        // IBAN
        '/\b[A-Z]{2}\d{2}[A-Z0-9]{4,30}\b/' => '[IBAN]',
        // Numéros de carte bancaire
        '/\b(?:\d{4}[\s\-]?){3}\d{4}\b/' => '[CARD]',
        // Montants financiers (ex: 1 500 000 XAF ou 25000 FCFA)
        '/\b\d[\d\s]*(?:XAF|FCFA|EUR|USD|CFA)\b/i' => '[AMOUNT]',
    ];

    /**
     * Scrube un tableau de messages avant envoi au LLM externe.
     *
     * @param  array<array{role: string, content: string}>  $messages
     * @return array<array{role: string, content: string}>
     */
    public function scrubMessages(array $messages): array
    {
        return array_map(function (array $message) {
            if (isset($message['content']) && is_string($message['content'])) {
                $message['content'] = $this->scrubString($message['content']);
            }

            return $message;
        }, $messages);
    }

    public function scrubString(string $text): string
    {
        foreach (self::PATTERNS as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        return $text;
    }

    /**
     * Scrube le userPrompt et retourne version nettoyée + version originale pour log interne.
     *
     * @return array{clean: string, had_pii: bool}
     */
    public function scrubPrompt(string $prompt): array
    {
        $clean = $this->scrubString($prompt);

        return [
            'clean' => $clean,
            'had_pii' => $clean !== $prompt,
        ];
    }
}

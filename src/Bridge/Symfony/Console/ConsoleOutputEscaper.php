<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Console;

use Symfony\Component\Console\Formatter\OutputFormatter;

final class ConsoleOutputEscaper
{
    public static function escapeInline(string $text): string
    {
        return OutputFormatter::escape(self::sanitizeInline($text));
    }

    public static function escapeMultiline(string $text): string
    {
        return OutputFormatter::escape(self::sanitizeMultiline($text));
    }

    public static function sanitizeInline(string $text): string
    {
        return self::escapeControlCharacters($text, false);
    }

    public static function sanitizeMultiline(string $text): string
    {
        return self::escapeControlCharacters($text, true);
    }

    /**
     * @param string[] $segments
     *
     * @return string[]
     */
    public static function escapeInlineCollection(array $segments): array
    {
        return array_map(static function (string $segment): string {
            return self::escapeInline($segment);
        }, $segments);
    }

    private static function escapeControlCharacters(string $text, bool $preserveLineBreaks): string
    {
        $pattern = $preserveLineBreaks ? '/[\x00-\x09\x0B-\x1F\x7F]/' : '/[\x00-\x1F\x7F]/';

        return (string) preg_replace_callback($pattern, static function (array $matches): string {
            return sprintf('\\x%02X', ord($matches[0]));
        }, $text);
    }
}

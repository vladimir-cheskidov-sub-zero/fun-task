<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console;

use FunTask\Bridge\Symfony\Console\ConsoleOutputEscaper;
use PHPUnit\Framework\TestCase;

final class ConsoleOutputEscaperTest extends TestCase
{
    public function testEscapeInlineEscapesControlCharactersAndConsoleMarkup(): void
    {
        self::assertSame(
            'bad\\x1B\\x0A\\<error\\>name\\</error\\>',
            ConsoleOutputEscaper::escapeInline("bad\x1B\n<error>name</error>")
        );
    }

    public function testEscapeMultilinePreservesLineBreaks(): void
    {
        self::assertSame(
            "line 1\nline 2\\x1B \\<comment\\>x\\</comment\\>",
            ConsoleOutputEscaper::escapeMultiline("line 1\nline 2\x1B <comment>x</comment>")
        );
    }

    public function testSanitizeInlineEscapesControlCharactersWithoutFormatterEscaping(): void
    {
        self::assertSame(
            'broken\\x1B\\x0A<error>name</error>',
            ConsoleOutputEscaper::sanitizeInline("broken\x1B\n<error>name</error>")
        );
    }

    public function testSanitizeMultilinePreservesLineBreaksWithoutFormatterEscaping(): void
    {
        self::assertSame(
            "line 1\nline 2\\x1B <comment>x</comment>",
            ConsoleOutputEscaper::sanitizeMultiline("line 1\nline 2\x1B <comment>x</comment>")
        );
    }

    public function testEscapeInlineCollectionEscapesEachSegment(): void
    {
        self::assertSame(
            ['first', 'sec\\x1Bond', '\\<info\\>third\\</info\\>'],
            ConsoleOutputEscaper::escapeInlineCollection(['first', "sec\x1Bond", '<info>third</info>'])
        );
    }
}

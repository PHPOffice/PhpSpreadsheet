<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

/**
 * Dom loadHtml usually succeeds even with invalid Html,
 * although it will generate warning messages.
 * This member demonstrates a method less intrusive than
 * set_error_handler to detect if there has been a problem.
 * I want it run in a separate
 * process because I am nervous about libxml_use_internal_errors.
 *
 * @runTestsInSeparateProcesses
 */
class HtmlLibxmlTest extends TestCase
{
    private bool $useErrors;

    protected function setUp(): void
    {
        $this->useErrors = libxml_use_internal_errors(true);
    }

    protected function tearDown(): void
    {
        libxml_use_internal_errors($this->useErrors);
    }

    public function testLoadInvalidString(): void
    {
        $html = '<table<>';
        (new Html())->loadFromString($html);
        self::assertNotEmpty(libxml_get_errors());
    }

    public function testLoadValidString(): void
    {
        $html = '<table>';
        (new Html())->loadFromString($html);
        self::assertEmpty(libxml_get_errors());
    }
}

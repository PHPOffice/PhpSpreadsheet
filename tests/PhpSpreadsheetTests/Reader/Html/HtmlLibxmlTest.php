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
 */
class HtmlLibxmlTest extends TestCase
{
    public function testLoadInvalidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table<>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $reader->loadFromString($html);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadValidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadValidStringNoSuppress(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }
}

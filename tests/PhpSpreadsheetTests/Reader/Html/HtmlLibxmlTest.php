<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

/**
 * Dom loadHtml usually succeeds even with invalid Html,
 * although it will generate warning messages.
 * This member demonstrates a method less intrusive than
 * set_error_handler to detect if there has been a problem.
 */
class HtmlLibxmlTest extends TestCase
{
    private Spreadsheet $spreadsheet;

    protected function tearDown(): void
    {
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function testLoadInvalidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table<>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $this->spreadsheet = $reader->loadFromString($html);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadValidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $this->spreadsheet = $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadValidStringNoSuppress(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $this->spreadsheet = $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadValidFile(): void
    {
        $libxml = libxml_use_internal_errors();
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $file = 'tests/data/Reader/HTML/charset.ISO-8859-1.html4.html';
        $this->spreadsheet = $reader->load($file);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }

    public function testLoadInvalidFile(): void
    {
        $libxml = libxml_use_internal_errors();
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $file = 'tests/data/Reader/HTML/badhtml.html';
        $this->spreadsheet = $reader->load($file);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
    }
}

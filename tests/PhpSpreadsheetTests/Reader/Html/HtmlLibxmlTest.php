<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Helper\Html as HelperHtml;
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
    public function testAll(): void
    {
        if (!HelperHtml::isLibxmlBuggy()) {
            self::markTestSkipped('Current version of libxml has invalidated these tests');
        }
        $this->xtestLoadInvalidString();
        $this->xtestLoadValidString();
        $this->xtestLoadValidStringNoSuppress();
        $this->xtestLoadValidFile();
        $this->xtestLoadInvalidFile();
    }

    public function xtestLoadInvalidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table<>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $spreadsheet = $reader->loadFromString($html);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function xtestLoadValidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $spreadsheet = $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function xtestLoadValidStringNoSuppress(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function xtestLoadValidFile(): void
    {
        $libxml = libxml_use_internal_errors();
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $file = 'tests/data/Reader/HTML/charset.ISO-8859-1.html4.html';
        $spreadsheet = $reader->load($file);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function xtestLoadInvalidFile(): void
    {
        $libxml = libxml_use_internal_errors();
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $file = 'tests/data/Reader/HTML/badhtml.html';
        $spreadsheet = $reader->load($file);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }
}

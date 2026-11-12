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
 * libxml 2.14.00 is more tolerant than earlier versions.
 * Html which generates warning messages before and after
 * includes the following samples:
 * <div></span>
 * <meta http-equiv="Content-Type" content="text/html; charset=invalid-charset">
 * <meta charset='UTF-8'>\xFF\xFE
 */
class HtmlLibxmlTest extends TestCase
{
    public function testLoadInvalidString(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<div></span>';
        $reader = new Html();
        $reader->setSuppressLoadWarnings(true);
        $spreadsheet = $reader->loadFromString($html);
        self::assertNotEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadValidString(): void
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

    public function testLoadValidStringNoSuppress(): void
    {
        $libxml = libxml_use_internal_errors();
        $html = '<table>';
        $reader = new Html();
        $spreadsheet = $reader->loadFromString($html);
        self::assertEmpty($reader->getLibxmlMessages());
        self::assertSame($libxml, libxml_use_internal_errors());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadValidFile(): void
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

    public function testLoadInvalidFile(): void
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

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class HyperlinkTest extends AbstractFunctional
{
    public function testSaveAndLoadHyperlinks(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $spreadsheetOld->getProperties()->setCompany('g</meta:user-defined>zorg');
        $spreadsheetOld->getProperties()->setCategory('h</meta:user-defined>zorg');
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->getCell('A1')->setValue('Hello World');
        $sheet->getCell('A2')->setValue('http://example.org');
        $sheet->getCell('A2')->getHyperlink()->setUrl('http://example.org/');
        $sheet->getCell('A3')->setValue('pa<ge1');
        $sheet->getCell('A3')->getHyperlink()->setUrl('http://example.org/page1.html');
        $sheet2 = $spreadsheetOld->createSheet();
        $sheet2->setTitle('TargetSheet');
        $sheet2->setCellValue('B4', 'TargetCell');
        $sheet2->setCellValue('B3', 'not target');
        $sheet->getCell('A4')->setValue('go to Target');
        $sheet->getCell('A4')->getHyperlink()->setUrl('sheet://TargetSheet!B4');
        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();

        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame('g</meta:user-defined>zorg', $spreadsheet->getProperties()->getCompany());
        self::assertSame('h</meta:user-defined>zorg', $spreadsheet->getProperties()->getCategory());
        self::assertSame('http://example.org', $newSheet->getCell('A2')->getValue());
        self::assertSame('http://example.org/', $newSheet->getCell('A2')->getHyperlink()->getUrl());
        self::assertSame('pa<ge1', $newSheet->getCell('A3')->getValue());
        self::assertSame('http://example.org/page1.html', $newSheet->getCell('A3')->getHyperlink()->getUrl());
        self::assertSame('go to Target', $newSheet->getCell('A4')->getValue());
        self::assertSame('sheet://TargetSheet!B4', $newSheet->getCell('A4')->getHyperlink()->getUrl());

        // Verify that http links are unchanged,
        //   but internal sheet link has changed.
        $writer = new Ods($spreadsheet);
        $content = $writer->getWriterPartContent()->write();
        self::assertStringContainsString('xlink:href="http://example.org/"', $content);
        self::assertStringContainsString('xlink:href="http://example.org/page1.html"', $content);
        self::assertStringContainsString('xlink:href="#TargetSheet.B4"', $content);
        self::assertStringNotContainsString('sheet:', $content);

        $spreadsheet->disconnectWorksheets();
    }

    private const INTERNAL_LINKS = [
        'A1' => ['sheet://Sheet2!A1', '#Sheet2.A1'],
        'A2' => ['sheet://Sheet2!A1:B3', '#Sheet2.A1:B3'],
        'A3' => ["sheet://'My Sheet'!C3", "#'My Sheet'.C3"],
        'A4' => ["sheet://'a.b'!D4", "#'a.b'.D4"],
        'A5' => ['sheet://MyName', '#MyName'],
    ];

    public function testReadInternalLinks(): void
    {
        // Written by LibreOffice, which keeps the '!' of 'My Sheet'!C3 from the Xlsx it was converted from
        $spreadsheet = (new OdsReader())->load('tests/data/Reader/Ods/InternalLinks.ods');
        $sheet = $spreadsheet->getSheetByNameOrThrow('Main');
        foreach (self::INTERNAL_LINKS as $coordinate => [$url]) {
            self::assertSame($url, $sheet->getCell($coordinate)->getHyperlink()->getUrl(), $coordinate);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteInternalLinks(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        foreach (['Sheet2', 'My Sheet', 'a.b'] as $title) {
            $spreadsheetOld->createSheet()->setTitle($title);
        }
        $spreadsheetOld->addNamedRange(new NamedRange('MyName', $spreadsheetOld->getSheetByNameOrThrow('Sheet2'), '$B$2'));
        foreach (self::INTERNAL_LINKS as $coordinate => [$url]) {
            $sheet->setCellValue($coordinate, 'link');
            $sheet->getCell($coordinate)->getHyperlink()->setUrl($url);
        }

        $content = (new Ods($spreadsheetOld))->getWriterPartContent()->write();
        foreach (self::INTERNAL_LINKS as [, $href]) {
            self::assertStringContainsString('xlink:href="' . $href . '"', $content);
        }

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();
        $newSheet = $spreadsheet->getActiveSheet();
        foreach (self::INTERNAL_LINKS as $coordinate => [$url]) {
            self::assertSame($url, $newSheet->getCell($coordinate)->getHyperlink()->getUrl(), $coordinate);
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testHyperlinkOnNonStringCells(): void
    {
        $spreadsheetOld = new Spreadsheet();
        $sheet = $spreadsheetOld->getActiveSheet();
        $sheet->setCellValue('A1', 42);
        $sheet->setCellValue('A2', true);
        $sheet->setCellValue('A3', '=1+1');
        $sheet->setCellValue('A4', '="t"&"x"');
        $sheet->setCellValue('A5', 'plain');
        foreach (range(1, 4) as $row) {
            $sheet->getCell("A$row")->getHyperlink()->setUrl("https://example.org/$row");
        }

        $content = (new Ods($spreadsheetOld))->getWriterPartContent()->write();
        self::assertStringContainsString('office:value="42"><text:p><text:a xlink:href="https://example.org/1" xlink:type="simple">42</text:a></text:p>', $content);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();
        $newSheet = $spreadsheet->getActiveSheet();
        self::assertSame(42, $newSheet->getCell('A1')->getValue());
        foreach (range(1, 4) as $row) {
            self::assertSame("https://example.org/$row", $newSheet->getCell("A$row")->getHyperlink()->getUrl(), "A$row");
        }
        self::assertFalse($newSheet->getCell('A5')->hasHyperlink());
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadLinkFromCellStyle(): void
    {
        // Converted by LibreOffice from a Xlsx with links on a number, percentage, boolean, formula, text formula and date
        // cell; it keeps them in the cell style, and A1 has no style of its own but the default style of its column
        foreach ([false, true] as $readDataOnly) {
            $reader = new OdsReader();
            $reader->setReadDataOnly($readDataOnly);
            $spreadsheet = $reader->load('tests/data/Reader/Ods/StyleHyperlinks.ods');
            $sheet = $spreadsheet->getActiveSheet();
            foreach (range(1, 6) as $row) {
                self::assertSame("https://example.org/$row", $sheet->getCell("A$row")->getHyperlink()->getUrl(), "A$row");
            }
            self::assertFalse($sheet->getCell('B1')->hasHyperlink());
            $spreadsheet->disconnectWorksheets();
        }
    }
}

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
}

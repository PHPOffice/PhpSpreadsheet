<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4584Test extends AbstractFunctional
{
    public function testWriteRowDimensions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->setCellValue('A1', 'hello there world 1');
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setCustomFormat(true);
        $sheet->setCellValue('A2', 'hello there world 2');
        $sheet->setCellValue('A4', 'hello there world 4');
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString(
            '<sheetFormatPr customHeight="true" defaultRowHeight="20" outlineLevelRow="0" outlineLevelCol="0"/>',
            $data
        );
        self::assertStringContainsString(
            '<row r="1" spans="1:1" customFormat="1" ht="-1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="2" spans="1:1" customHeight="1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="4" spans="1:1" customHeight="1">',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteRowDimensionsNoCustom(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->setCellValue('A1', 'hello there world 1');
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        //$sheet->getRowDimension(1)->setCustomFormat(true);
        $sheet->setCellValue('A2', 'hello there world 2');
        $sheet->setCellValue('A4', 'hello there world 4');
        $writer = new XlsxWriter($spreadsheet);
        $writerWorksheet = new XlsxWriter\Worksheet($writer);
        $data = $writerWorksheet->writeWorksheet($sheet, []);
        self::assertStringContainsString(
            '<sheetFormatPr customHeight="true" defaultRowHeight="20" outlineLevelRow="0" outlineLevelCol="0"/>',
            $data
        );
        self::assertStringContainsString(
            '<row r="1" spans="1:1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="2" spans="1:1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="4" spans="1:1">',
            $data
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteAndReadRowDimension(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->setCellValue('A1', 'hello there world 1');
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setCustomFormat(true);
        $sheet->setCellValue('A2', 'hello there world 2');
        $sheet->setCellValue('A4', 'hello there world 4');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $rwriter = new XlsxWriter($reloadedSpreadsheet);
        $rwriterWorksheet = new XlsxWriter\Worksheet($rwriter);
        $data = $rwriterWorksheet->writeWorksheet($rsheet, []);
        self::assertStringContainsString(
            '<sheetFormatPr customHeight="true" defaultRowHeight="20" outlineLevelRow="0" outlineLevelCol="0"/>',
            $data
        );
        self::assertStringContainsString(
            '<row r="1" spans="1:1" customFormat="1" ht="-1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="2" spans="1:1" customHeight="1">',
            $data
        );
        self::assertStringContainsString(
            '<row r="4" spans="1:1" customHeight="1">',
            $data
        );
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testBadOutlineLevel(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Outline level must range between 0 and 7.');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getRowDimension(1)->setOutlineLevel(8);
    }
}

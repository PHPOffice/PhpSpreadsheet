<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class StyleTest extends TestCase
{
    public function testStyleOddMethods(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCoordinate = 'A1';
        $cell1 = $sheet->getCell($cellCoordinate);
        $cell1style = $cell1->getStyle();
        self::assertSame($spreadsheet, $cell1style->getParent());
        $styleArray = ['alignment' => ['textRotation' => 45]];
        $outArray = $cell1style->getStyleArray($styleArray);
        self::assertEquals($styleArray, $outArray['quotePrefix']);
    }

    public function testStyleColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCoordinates = 'A:B';
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle($cellCoordinates)->applyFromArray($styleArray);
        $sheet->setCellValue('A1', 'xxxa1');
        $sheet->setCellValue('A2', 'xxxa2');
        $sheet->setCellValue('A3', 'xxxa3');
        $sheet->setCellValue('B1', 'xxxa1');
        $sheet->setCellValue('B2', 'xxxa2');
        $sheet->setCellValue('B3', 'xxxa3');
        $sheet->setCellValue('C1', 'xxxc1');
        $sheet->setCellValue('C2', 'xxxc2');
        $sheet->setCellValue('C3', 'xxxc3');
        $styleArray = [
            'font' => [
                'italic' => true,
            ],
        ];
        $sheet->getStyle($cellCoordinates)->applyFromArray($styleArray);
        self::assertTrue($sheet->getStyle('A1')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('B2')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('C3')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('A1')->getFont()->getItalic());
        self::assertTrue($sheet->getStyle('B2')->getFont()->getItalic());
        self::assertFalse($sheet->getStyle('C3')->getFont()->getItalic());
    }

    public function testStyleIsReused(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $styleArray = [
            'font' => [
                'italic' => true,
            ],
        ];

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setItalic(true);

        $sheet->getStyle('A')->applyFromArray($styleArray);

        self::assertCount(4, $spreadsheet->getCellXfCollection());
        $spreadsheet->garbageCollect();

        self::assertCount(3, $spreadsheet->getCellXfCollection());
    }

    public function testStyleRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCoordinates = '2:3';
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle($cellCoordinates)->applyFromArray($styleArray);
        $sheet->setCellValue('A1', 'xxxa1');
        $sheet->setCellValue('A2', 'xxxa2');
        $sheet->setCellValue('A3', 'xxxa3');
        $sheet->setCellValue('B1', 'xxxa1');
        $sheet->setCellValue('B2', 'xxxa2');
        $sheet->setCellValue('B3', 'xxxa3');
        $sheet->setCellValue('C1', 'xxxc1');
        $sheet->setCellValue('C2', 'xxxc2');
        $sheet->setCellValue('C3', 'xxxc3');
        $styleArray = [
            'font' => [
                'italic' => true,
            ],
        ];
        $sheet->getStyle($cellCoordinates)->applyFromArray($styleArray);
        self::assertFalse($sheet->getStyle('A1')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('B2')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('C3')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('A1')->getFont()->getItalic());
        self::assertTrue($sheet->getStyle('B2')->getFont()->getItalic());
        self::assertTrue($sheet->getStyle('C3')->getFont()->getItalic());
    }

    public function testIssue1712A(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rgb = '4467b8';
        $sheet->fromArray(['OK', 'KO']);
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($rgb);
        $spreadsheet->getActiveSheet()
            ->getStyle('B')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($rgb);
        self::assertEquals($rgb, $sheet->getCell('A1')->getStyle()->getFill()->getStartColor()->getRGB());
        self::assertEquals($rgb, $sheet->getCell('B1')->getStyle()->getFill()->getStartColor()->getRGB());
    }

    public function testIssue1712B(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $rgb = '4467b8';
        $spreadsheet->getActiveSheet()
            ->getStyle('A1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($rgb);
        $spreadsheet->getActiveSheet()
            ->getStyle('B')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($rgb);
        $sheet->fromArray(['OK', 'KO']);
        self::assertEquals($rgb, $sheet->getCell('A1')->getStyle()->getFill()->getStartColor()->getRGB());
        self::assertEquals($rgb, $sheet->getCell('B1')->getStyle()->getFill()->getStartColor()->getRGB());
    }

    public function testStyleLoopUpwards(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cellCoordinates = 'C5:A3';
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle($cellCoordinates)->applyFromArray($styleArray);
        $sheet->setCellValue('A1', 'xxxa1');
        $sheet->setCellValue('A2', 'xxxa2');
        $sheet->setCellValue('A3', 'xxxa3');
        $sheet->setCellValue('B1', 'xxxa1');
        $sheet->setCellValue('B2', 'xxxa2');
        $sheet->setCellValue('B3', 'xxxa3');
        $sheet->setCellValue('C1', 'xxxc1');
        $sheet->setCellValue('C2', 'xxxc2');
        $sheet->setCellValue('C3', 'xxxc3');
        self::assertFalse($sheet->getStyle('A1')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('B2')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('C3')->getFont()->getBold());
    }

    public function testStyleCellAddressObject(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $cellAddress = new CellAddress('A1', $worksheet);
        $style = $worksheet->getStyle($cellAddress);
        $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

        self::assertSame(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH, $style->getNumberFormat()->getFormatCode());
    }

    public function testStyleCellRangeObject(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $cellAddress1 = new CellAddress('A1', $worksheet);
        $cellAddress2 = new CellAddress('B2', $worksheet);
        $cellRange = new CellRange($cellAddress1, $cellAddress2);
        $style = $worksheet->getStyle($cellRange);
        $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

        self::assertSame(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH, $style->getNumberFormat()->getFormatCode());
    }
}

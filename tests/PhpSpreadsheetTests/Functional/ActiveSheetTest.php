<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class ActiveSheetTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Xls'],
        ];
    }

    /**
     * Test load file with correct active sheet.
     *
     * @dataProvider providerFormats
     */
    public function testActiveSheet(string $format): void
    {
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setTitle('Test1')
            ->setCellValue('D1', 1)
            ->setCellValue('D2', 2)
            ->setCellValue('D3', 3)
            ->setCellValue('D4', 4)
            ->setCellValue('D5', '=SUM(D1:D4)')
            ->setSelectedCell('B2');

        $spreadsheet->createSheet(1);

        $spreadsheet->setActiveSheetIndex(1)
            ->setTitle('Test2')
            ->setCellValue('D1', 4)
            ->setCellValue('E1', 3)
            ->setCellValue('F1', 2)
            ->setCellValue('G1', 1)
            ->setCellValue('H1', '=SUM(D1:G4)')
            ->setSelectedCells('A1:B2');

        $spreadsheet->createSheet(2);

        $spreadsheet->setActiveSheetIndex(2)
            ->setCellValue('F1', 2)
            ->getCell('F1')
            ->getStyle()
            ->getProtection()
            ->setHidden(Protection::PROTECTION_PROTECTED);
        $spreadsheet->setActiveSheetIndex(2)
            ->setTitle('Test3')
            ->setCellValue('A1', 4)
            ->setCellValue('B1', 3)
            ->setCellValue('C1', 2)
            ->setCellValue('D1', 1)
            ->setCellValue('E1', '=SUM(A1:D4)')
            ->setSelectedCells('A1:D1');

        $spreadsheet->setActiveSheetIndex(1);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Original object.
        self::assertSame(1, $spreadsheet->getActiveSheetIndex());

        // Saved and reloaded file.
        self::assertSame(1, $reloadedSpreadsheet->getActiveSheetIndex());
    }
}

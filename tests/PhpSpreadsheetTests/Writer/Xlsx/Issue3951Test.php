<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3951Test extends AbstractFunctional
{
    /**
     * @dataProvider providerType
     */
    public function testIssue2266(string $type): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('sorttrue');
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('B1')->setValue(15);
        $protection = $sheet->getProtection();
        $protection->setPassword('testpassword');
        $protection->setSheet(true);
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
        $protection->setObjects(true);
        $protection->setAutoFilter(false);
        $protection->setSort(true);

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('sortfalse');
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('B1')->setValue(15);
        $protection = $sheet->getProtection();
        $protection->setPassword('testpassword');
        $protection->setSheet(true);
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
        $protection->setObjects(true);
        $protection->setAutoFilter(false);
        $protection->setSort(false);

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('sortfalsenocolpw');
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('C1')->setValue(15);
        $protection = $sheet->getProtection();
        $protection->setPassword('testpassword');
        $protection->setSheet(true);
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
        $protection->setObjects(true);
        $protection->setAutoFilter(false);
        $protection->setSort(false);
        $sheet->protectCells('A:A');

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('sortfalsecolpw');
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(5);
        $sheet->getCell('C1')->setValue(15);
        $protection = $sheet->getProtection();
        $protection->setPassword('testpassword');
        $protection->setSheet(true);
        $protection->setInsertRows(true);
        $protection->setFormatCells(true);
        $protection->setObjects(true);
        $protection->setAutoFilter(false);
        $protection->setSort(false);
        $sheet->protectCells('A:A', 'sortpw', false, 'sortrange');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $type);
        $spreadsheet->disconnectWorksheets();

        self::assertCount(4, $reloadedSpreadsheet->getAllSheets());

        $rsheet1 = $reloadedSpreadsheet->getSheetByNameOrThrow('sorttrue');
        $protection = $rsheet1->getProtection();
        self::assertNotEquals('', $protection->getPassword());
        self::assertTrue($protection->getSort());
        self::assertEmpty($rsheet1->getProtectedCellRanges());

        $rsheet2 = $reloadedSpreadsheet->getSheetByNameOrThrow('sortfalse');
        $protection = $rsheet2->getProtection();
        self::assertNotEquals('', $protection->getPassword());
        self::assertFalse($protection->getSort());
        self::assertEmpty($rsheet2->getProtectedCellRanges());

        $rsheet3 = $reloadedSpreadsheet->getSheetByNameOrThrow('sortfalsenocolpw');
        $protection = $rsheet3->getProtection();
        self::assertNotEquals('', $protection->getPassword());
        self::assertFalse($protection->getSort());
        $maxRow = ($type === 'Xlsx') ? 1048576 : 65536;
        $testKey = "A1:A$maxRow";
        $protectedCells = $rsheet3->getProtectedCellRanges();
        self::assertCount(1, $protectedCells);
        self::assertArrayHasKey($testKey, $protectedCells);
        self::assertSame('', $protectedCells[$testKey]->getPassword());

        $rsheet4 = $reloadedSpreadsheet->getSheetByNameOrThrow('sortfalsecolpw');
        $protection = $rsheet4->getProtection();
        self::assertNotEquals('', $protection->getPassword());
        self::assertFalse($protection->getSort());
        $maxRow = ($type === 'Xlsx') ? 1048576 : 65536;
        $testKey = "A1:A$maxRow";
        $protectedCells = $rsheet4->getProtectedCellRanges();
        self::assertCount(1, $protectedCells);
        self::assertArrayHasKey($testKey, $protectedCells);
        self::assertNotEquals('', $protectedCells[$testKey]->getPassword());
        if ($type === 'Xlsx') {
            self::assertSame('sortrange', $protectedCells[$testKey]->getName());
        }

        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public static function providerType(): array
    {
        return [
            ['Xlsx'],
            ['Xls'],
        ];
    }
}

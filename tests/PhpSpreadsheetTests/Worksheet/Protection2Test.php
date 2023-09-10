<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PHPUnit\Framework\TestCase;

class Protection2Test extends TestCase
{
    public function testisHiddenOnFormulaBar(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('X')
            ->getStyle()->getProtection()
            ->setHidden(Protection::PROTECTION_UNPROTECTED);
        $sheet->getCell('A2')->setValue('=SUM(1,2)')
            ->getStyle()->getProtection()
            ->setHidden(Protection::PROTECTION_UNPROTECTED);
        $sheet->getCell('B1')->setValue('X')
            ->getStyle()->getProtection()
            ->setHidden(Protection::PROTECTION_PROTECTED);
        $sheet->getCell('B2')->setValue('=SUM(1,2)')
            ->getStyle()->getProtection()
            ->setHidden(Protection::PROTECTION_PROTECTED);
        $sheet->getCell('C1')->setValue('X');
        $sheet->getCell('C2')->setValue('=SUM(1,2)');
        self::assertFalse($sheet->getCell('A1')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('A2')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('B1')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('B2')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('C1')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('C2')->isHiddenOnFormulaBar());
        $sheetProtection = $sheet->getProtection();
        $sheetProtection->setSheet(true);
        self::assertFalse($sheet->getCell('A1')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('A2')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('B1')->isHiddenOnFormulaBar(), 'not a formula1');
        self::assertTrue($sheet->getCell('B2')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('C1')->isHiddenOnFormulaBar(), 'not a formula2');
        self::assertTrue($sheet->getCell('C2')->isHiddenOnFormulaBar());
        self::assertFalse($sheet->getCell('D1')->isHiddenOnFormulaBar(), 'uninitialized cell is not formula');
        $spreadsheet->disconnectWorksheets();
    }

    public function testisLocked(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('X')
            ->getStyle()->getProtection()
            ->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getCell('A2')->setValue('=SUM(1,2)')
            ->getStyle()->getProtection()
            ->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getCell('B1')->setValue('X')
            ->getStyle()->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getCell('B2')->setValue('=SUM(1,2)')
            ->getStyle()->getProtection()
            ->setLocked(Protection::PROTECTION_PROTECTED);
        $sheet->getCell('C1')->setValue('X');
        $sheet->getCell('C2')->setValue('=SUM(1,2)');
        self::assertFalse($sheet->getCell('A1')->isLocked());
        self::assertFalse($sheet->getCell('A2')->isLocked());
        self::assertFalse($sheet->getCell('B1')->isLocked());
        self::assertFalse($sheet->getCell('B2')->isLocked());
        self::assertFalse($sheet->getCell('C1')->isLocked());
        self::assertFalse($sheet->getCell('C2')->isLocked());
        $sheetProtection = $sheet->getProtection();
        $sheetProtection->setSheet(true);
        self::assertFalse($sheet->getCell('A1')->isLocked());
        self::assertFalse($sheet->getCell('A2')->isLocked());
        self::assertTrue($sheet->getCell('B1')->isLocked());
        self::assertTrue($sheet->getCell('B2')->isLocked());
        self::assertTrue($sheet->getCell('C1')->isLocked());
        self::assertTrue($sheet->getCell('C2')->isLocked());
        self::assertTrue($sheet->getCell('D1')->isLocked(), 'uninitialized cell');
        $spreadsheet->disconnectWorksheets();
    }
}

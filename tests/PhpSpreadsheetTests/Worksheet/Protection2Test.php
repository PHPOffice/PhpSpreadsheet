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

    /**
     * Same as above, but uses    $sheet->isCellHiddenOnFormulaBar
     *                rather than $sheet->getCell()->isHiddenOnFormulaBar().
     */
    public function testisHiddenOnFormulaBar2(): void
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
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('A1'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('A2'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('B1'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('B2'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('C1'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('C2'));
        $sheetProtection = $sheet->getProtection();
        $sheetProtection->setSheet(true);
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('A1'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('A2'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('B1'), 'not a formula1');
        self::assertTrue($sheet->isCellHiddenOnFormulaBar('B2'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('C1'), 'not a formula2');
        self::assertTrue($sheet->isCellHiddenOnFormulaBar('C2'));
        self::assertFalse($sheet->isCellHiddenOnFormulaBar('D1'), 'uninitialized cell is not formula');
        self::assertFalse($sheet->cellExists('D1'));
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

    /**
     * Same as above, but uses    $sheet->isCellLocked
     *                rather than $sheet->getCell()->isLocked().
     */
    public function testisLocked2(): void
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
        self::assertFalse($sheet->isCellLocked('A1'));
        self::assertFalse($sheet->isCellLocked('A2'));
        self::assertFalse($sheet->isCellLocked('B1'));
        self::assertFalse($sheet->isCellLocked('B2'));
        self::assertFalse($sheet->isCellLocked('C1'));
        self::assertFalse($sheet->isCellLocked('C2'));
        $sheetProtection = $sheet->getProtection();
        $sheetProtection->setSheet(true);
        self::assertFalse($sheet->isCellLocked('A1'));
        self::assertFalse($sheet->isCellLocked('A2'));
        self::assertTrue($sheet->isCellLocked('B1'));
        self::assertTrue($sheet->isCellLocked('B2'));
        self::assertTrue($sheet->isCellLocked('C1'));
        self::assertTrue($sheet->isCellLocked('C2'));
        self::assertTrue($sheet->isCellLocked('D1'), 'uninitialized cell');
        self::assertFalse($sheet->cellExists('D1'));
        $spreadsheet->disconnectWorksheets();
    }
}

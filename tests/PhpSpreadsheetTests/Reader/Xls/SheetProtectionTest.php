<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class SheetProtectionTest extends AbstractFunctional
{
    public function testSheetProtection(): void
    {
        $originalSpreadsheet = new Spreadsheet();
        $oldSheet = $originalSpreadsheet->getActiveSheet();
        $oldProtection = $oldSheet->getProtection();
        $oldProtection->setPassword('PhpSpreadsheet');
        // setSheet should be true in order to enable protection!
        $oldProtection->setSheet(true);
        // The following are set to false, i.e. user is allowed to
        //    sort, insert rows, or format cells without unprotecting sheet.
        $oldProtection->setSort(false);
        $oldProtection->setInsertRows(false);
        $oldProtection->setFormatCells(false);
        $oldProtection->setFormatCells(false);
        // Defaults below are true
        $oldProtection->setSelectLockedCells(true);
        $oldProtection->setSelectUnlockedCells(false);
        $oldProtection->setScenarios(false);
        $oldProtection->setObjects(true);
        $spreadsheet = $this->writeAndReload($originalSpreadsheet, 'Xls');
        $originalSpreadsheet->disconnectWorksheets();
        $newSheet = $spreadsheet->getActiveSheet();
        $protection = $newSheet->getProtection();
        self::assertTrue($protection->verify('PhpSpreadsheet'));
        self::assertTrue($protection->getSheet());
        self::assertFalse($protection->getSort());
        self::assertFalse($protection->getInsertRows());
        self::assertFalse($protection->getFormatCells());
        self::assertNotFalse($protection->getInsertColumns());
        self::assertTrue($protection->getSelectLockedCells());
        self::assertFalse($protection->getSelectUnlockedCells());
        self::assertTrue($protection->getObjects());
        self::assertFalse($protection->getScenarios());
        $spreadsheet->disconnectWorksheets();
    }
}

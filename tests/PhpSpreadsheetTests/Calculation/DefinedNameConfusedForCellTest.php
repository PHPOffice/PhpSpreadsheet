<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PHPUnit\Framework\TestCase;

class DefinedNameConfusedForCellTest extends TestCase
{
    const FILENAM = 'calcerror';
    const TYP = 'Xlsx';

    private static function getfilename()
    {
        return self::FILENAM . '.' . strtolower(self::TYP);
    }

    public function tearDown()
    {
        $out = self::getfilename();
        if (file_exists($out)) {
            unlink($out);
        }
    }

    public function testDefinedName()
    {
        $obj = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('A1A', $sheet0, 'A1'));
        $sheet0->setCellValue('B1', '=2*A1A');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, self::TYP);
        $out = self::getfilename();
        $writer->save($out);
        self::assertTrue(true);
    }
}

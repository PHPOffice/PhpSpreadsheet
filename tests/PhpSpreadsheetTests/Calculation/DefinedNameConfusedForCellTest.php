<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class DefinedNameConfusedForCellTest extends TestCase
{
    public function testDefinedName()
    {
        $obj = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('A1A', $sheet0, 'A1'));
        $sheet0->setCellValue('B1', '=2*A1A');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, 'Xlsx');
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer->save($filename);
        self::assertTrue(true);
    }
}

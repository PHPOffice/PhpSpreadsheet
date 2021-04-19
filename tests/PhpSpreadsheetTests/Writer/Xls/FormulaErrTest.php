<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class FormulaErrTest extends TestCase
{
    public function testFormulaError(): void
    {
        $obj = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new NamedRange('DEFNAM', $sheet0, '$A$1'));
        $sheet0->setCellValue('B1', '=2*DEFNAM');
        $sheet0->setCellValue('C1', '=DEFNAM=2');
        $sheet0->setCellValue('D1', '=CONCAT("X",DEFNAM)');
        $writer = IOFactory::createWriter($obj, 'Xls');
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $reader = IOFactory::createReader('Xls');
        $robj = $reader->load($filename);
        unlink($filename);
        $sheet0 = $robj->setActiveSheetIndex(0);
        $a1 = $sheet0->getCell('A1')->getCalculatedValue();
        self::assertEquals(2, $a1);
        $b1 = $sheet0->getCell('B1')->getCalculatedValue();
        self::assertEquals(4, $b1);
        $c1 = $sheet0->getCell('C1')->getCalculatedValue();
        $tru = true;
        self::assertEquals($tru, $c1);
        $d1 = $sheet0->getCell('D1')->getCalculatedValue();
        self::assertEquals('X2', $d1);
    }
}

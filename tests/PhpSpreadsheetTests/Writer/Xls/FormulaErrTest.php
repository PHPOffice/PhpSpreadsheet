<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls\Workbook;

use PHPUnit\Framework\TestCase;

class FormulaErrTest extends TestCase
{
    const FILENAM = 'formulaerr';
    const TYP = 'Xls';

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

    public function testFormulaError()
    {
        $obj = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('DEFNAM', $sheet0, 'A1'));
        $sheet0->setCellValue('B1', '=2*DEFNAM');
        $outtype = self::TYP;
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($obj, $outtype);
        $out = self::getfilename();
        $writer->save($out);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($outtype);
        $robj = $reader->load($out);
        $sheet0 = $robj->setActiveSheetIndex(0);
        $a1 = $sheet0->getCell('A1')->getCalculatedValue();
        self::assertEquals(2, $a1);
        $b1 = $sheet0->getCell('B1')->getCalculatedValue();
        self::assertEquals(4, $b1);
    }
}

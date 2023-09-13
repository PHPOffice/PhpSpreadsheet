<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefinedNameConfusedForCellTest extends TestCase
{
    public function testDefinedName(): void
    {
        $obj = new Spreadsheet();
        $sheet0 = $obj->setActiveSheetIndex(0);
        $sheet0->setCellValue('A1', 2);
        $obj->addNamedRange(new NamedRange('A1A', $sheet0, 'A1'));
        $sheet0->setCellValue('B1', '=2*A1A');
        $writer = IOFactory::createWriter($obj, 'Xlsx');
        $filename = File::temporaryFilename();
        $writer->save($filename);
        self::assertTrue(true);
        unlink($filename);
    }
}

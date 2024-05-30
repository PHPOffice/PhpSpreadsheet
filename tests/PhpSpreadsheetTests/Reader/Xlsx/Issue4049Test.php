<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue4049Test extends AbstractFunctional
{
    public function testColorScale(): void
    {
        $xlsxFile = 'tests/data/Reader/XLSX/issue.4049.xlsx';
        $reader = new Xlsx();
        $oldSpreadsheet = $reader->load($xlsxFile);
        $spreadsheet = $this->writeAndReload($oldSpreadsheet, 'Xlsx');
        $oldSpreadsheet->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        $conditionals = $sheet->getConditionalStylesCollection();
        self::assertCount(1, $conditionals);
        self::assertSame('E9:E14', array_keys($conditionals)[0]);
        $cond1 = $conditionals['E9:E14'];
        self::assertCount(1, $cond1);
        self::assertSame('colorScale', $cond1[0]->getConditionType());
        $colorScale = $cond1[0]->getColorScale();
        self::assertNotNull($colorScale);
        $min = $colorScale->getMinimumConditionalFormatValueObject();
        self::assertSame('formula', $min->getType());
        self::assertSame('25', $min->getCellFormula());

        self::assertNull($colorScale->getMidpointConditionalFormatValueObject());

        $max = $colorScale->getMaximumConditionalFormatValueObject();
        self::assertSame('max', $max->getType());
        self::assertNull($max->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}

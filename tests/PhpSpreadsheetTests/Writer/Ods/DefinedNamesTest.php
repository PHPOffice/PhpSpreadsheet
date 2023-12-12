<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DefinedNamesTest extends AbstractFunctional
{
    public function testDefinedNamesWriter(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $dataSet = [
            [7, 'x', 5],
            ['=', '=FORMULA'],
        ];
        $worksheet->fromArray($dataSet, null, 'A1');

        $spreadsheet->addDefinedName(new NamedRange('FIRST', $worksheet, '$A$1'));
        $spreadsheet->addDefinedName(new NamedRange('SECOND', $worksheet, '$C$1'));
        $spreadsheet->addDefinedName(new NamedFormula('FORMULA', $worksheet, '=FIRST*SECOND'));

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods');

        self::assertSame(7, $reloaded->getActiveSheet()->getCell('FIRST')->getValue());
        self::assertSame(5, $reloaded->getActiveSheet()->getCell('SECOND')->getValue());
        self::assertSame(35, $reloaded->getActiveSheet()->getCell('B2')->getCalculatedValue());
    }
}

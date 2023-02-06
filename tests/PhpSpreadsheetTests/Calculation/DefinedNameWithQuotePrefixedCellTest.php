<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefinedNameWithQuotePrefixedCellTest extends TestCase
{
    public function testDefinedNameIsAlwaysEvaluated(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet2->getCell('A1')->setValue('July 2019');
        $sheet2->getStyle('A1')
            ->setQuotePrefix(true);
        $sheet2->getCell('A2')->setValue(3);
        $spreadsheet->addNamedRange(new NamedRange('FM', $sheet2, '$A$2'));
        $sheet1->getCell('A1')->setValue('=(A2+FM)');
        $sheet1->getCell('A2')->setValue(38.42);

        self::assertSame(41.42, $sheet1->getCell('A1')->getCalculatedValue());
    }
}

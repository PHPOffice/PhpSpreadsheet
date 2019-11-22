<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    /**
     * @dataProvider providerSetValueExplicit
     *
     * @param mixed $expected
     * @param mixed $value
     * @param string $dataType
     */
    public function testSetValueExplicit($expected, $value, string $dataType)
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);

        self::assertSame($expected, $cell->getValue());
    }

    public function providerSetValueExplicit()
    {
        return require 'data/Cell/SetValueExplicit.php';
    }
}

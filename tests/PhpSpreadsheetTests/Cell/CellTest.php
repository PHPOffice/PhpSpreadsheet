<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    /**
     * @dataProvider providerSetValueExplicit
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testSetValueExplicit($expected, $value, string $dataType): void
    {
        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);

        self::assertSame($expected, $cell->getValue());
    }

    public function providerSetValueExplicit()
    {
        return require 'tests/data/Cell/SetValueExplicit.php';
    }

    /**
     * @dataProvider providerSetValueExplicitException
     *
     * @param mixed $value
     */
    public function testSetValueExplicitException($value, string $dataType): void
    {
        $this->expectException(Exception::class);

        $spreadsheet = new Spreadsheet();
        $cell = $spreadsheet->getActiveSheet()->getCell('A1');
        $cell->setValueExplicit($value, $dataType);
    }

    public function providerSetValueExplicitException()
    {
        return require 'tests/data/Cell/SetValueExplicitException.php';
    }
}

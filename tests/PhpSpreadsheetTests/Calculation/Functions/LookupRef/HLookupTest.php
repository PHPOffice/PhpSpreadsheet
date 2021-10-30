<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class HLookupTest extends TestCase
{
    /**
     * @dataProvider providerHLOOKUP
     *
     * @param mixed $expectedResult
     * @param mixed $lookup
     * @param mixed $rowIndex
     */
    public function testHLOOKUP($expectedResult, $lookup, array $values, $rowIndex, ?bool $rangeLookup = null): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $maxRow = 0;
        $maxCol = 0;
        $maxColLetter = 'A';
        $row = 0;
        foreach ($values as $rowValues) {
            ++$row;
            ++$maxRow;
            $col = 0;
            if (!is_array($rowValues)) {
                $rowValues = [$rowValues];
            }
            foreach ($rowValues as $cellValue) {
                ++$col;
                $colLetter = Coordinate::stringFromColumnIndex($col);
                if ($col > $maxCol) {
                    $maxCol = $col;
                    $maxColLetter = $colLetter;
                }
                if ($cellValue !== null) {
                    $sheet->getCell("$colLetter$row")->setValue($cellValue);
                }
            }
        }

        $boolArg = self::parseRangeLookup($rangeLookup);
        $sheet->getCell('ZZ8')->setValue($lookup);
        $sheet->getCell('ZZ7')->setValue($rowIndex);
        $sheet->getCell('ZZ1')->setValue("=HLOOKUP(ZZ8, A1:$maxColLetter$maxRow, ZZ7$boolArg)");
        self::assertEquals($expectedResult, $sheet->getCell('ZZ1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    private static function parseRangeLookup(?bool $rangeLookup): string
    {
        if ($rangeLookup === null) {
            return '';
        }

        return $rangeLookup ? ', true' : ', false';
    }

    public function providerHLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/HLOOKUP.php';
    }

    public function testGrandfathered(): void
    {
        // Second parameter is supposed to be array of arrays.
        // Some old tests called function directly using array of strings;
        // ensure these work as before.
        $expectedResult = '#REF!';
        $result = LookupRef::HLOOKUP(
            'Selection column',
            ['Selection column', 'Value to retrieve'],
            5,
            false
        );
        self::assertSame($expectedResult, $result);
        $expectedResult = 'Value to retrieve';
        $result = LookupRef::HLOOKUP(
            'Selection column',
            ['Selection column', 'Value to retrieve'],
            2,
            false
        );
        self::assertSame($expectedResult, $result);
    }
}

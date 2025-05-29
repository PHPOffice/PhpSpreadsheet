<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\Attributes\DataProvider;

class TocolTest extends AllSetupTeardown
{
    #[DataProvider('providerTocol')]
    public function testTorow(mixed $expectedResult, mixed $ignore = 'omitted', mixed $byColumn = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $this->setArrayAsArray();
        if (is_string($ignore) && $ignore !== 'omitted') {
            $ignore = '"' . $ignore . '"';
        }
        if (is_string($byColumn) && $byColumn !== 'omitted') {
            $byColumn = '"' . $byColumn . '"';
        }
        $ignore = StringHelper::convertToString($ignore);
        $byColumn = StringHelper::convertToString($byColumn, convertBool: true);
        if ($ignore === 'omitted') {
            $formula = '=TOCOL(A1:D3)';
        } elseif ($byColumn === 'omitted') {
            $formula = "=TOCOL(A1:D3,$ignore)";
        } else {
            $formula = "=TOCOL(A1:D3,$ignore,$byColumn)";
        }

        $data = [
            ['a-one', 'b-one', 'c-one', 'd-one'],
            [null, 'b-two', 'c-two', '=2/0'],
            [' ', 'b-three', 'c-three', 'd-three'],
        ];
        $sheet->fromArray($data, null, 'A1', true);
        $sheet->setCellValue('A5', $formula);
        $result = $sheet->getCell('A5')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerTocol(): array
    {
        return [
            'defaults' => [[['a-one'], ['b-one'], ['c-one'], ['d-one'], [0], ['b-two'], ['c-two'], ['#DIV/0!'], [' '], ['b-three'], ['c-three'], ['d-three']]],
            'ignore=0' => [[['a-one'], ['b-one'], ['c-one'], ['d-one'], [0], ['b-two'], ['c-two'], ['#DIV/0!'], [' '], ['b-three'], ['c-three'], ['d-three']], 0],
            'ignore=1 supplied as 1.1' => [[['a-one'], ['b-one'], ['c-one'], ['d-one'], ['b-two'], ['c-two'], ['#DIV/0!'], [' '], ['b-three'], ['c-three'], ['d-three']], 1.1],
            'ignore=2' => [[['a-one'], ['b-one'], ['c-one'], ['d-one'], [0], ['b-two'], ['c-two'], [' '], ['b-three'], ['c-three'], ['d-three']], 2],
            'ignore=3' => [[['a-one'], ['b-one'], ['c-one'], ['d-one'], ['b-two'], ['c-two'], [' '], ['b-three'], ['c-three'], ['d-three']], 3],
            'ignore=4 invalid' => ['#VALUE!', 4],
            'ignore=string invalid' => ['#VALUE!', 'x'],
            'by column' => [[['a-one'], [0], [' '], ['b-one'], ['b-two'], ['b-three'], ['c-one'], ['c-two'], ['c-three'], ['d-one'], ['#DIV/0!'], ['d-three']], 0, true],
            'by column using float rather than bool, ignore=2' => [[['a-one'], [0], [' '], ['b-one'], ['b-two'], ['b-three'], ['c-one'], ['c-two'], ['c-three'], ['d-one'], ['d-three']], 2, 29.7],
        ];
    }
}

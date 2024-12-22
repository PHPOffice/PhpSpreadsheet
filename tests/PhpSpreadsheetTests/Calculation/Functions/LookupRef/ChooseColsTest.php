<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PHPUnit\Framework\Attributes\DataProvider;

class ChooseColsTest extends AllSetupTeardown
{
    #[DataProvider('providerChooseCols')]
    public function testChooseCols(mixed $expectedResult, string $formula): void
    {
        Calculation::setArrayReturnType(
            Calculation::RETURN_ARRAY_AS_ARRAY
        );
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->fromArray(
            [
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                ['g', 'h', 'i'],
                ['j', 'k', 'l'],
                ['m', 'n', 'o'],
                ['p', 'q', 'r'],
                ['s', 't', 'u'],
                ['v', 'w', 'x'],
                ['y', 'z', '#'],
            ],
            null,
            'B3',
            true
        );
        $this->getSpreadsheet()->addNamedRange(
            new NamedRange(
                'definedname',
                $sheet,
                '$B$3:$D$11'
            )
        );

        $sheet->setCellValue('F3', $formula);
        $result = $sheet->getCell('F3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerChooseCols(): array
    {
        return require 'tests/data/Calculation/LookupRef/CHOOSECOLS.php';
    }
}

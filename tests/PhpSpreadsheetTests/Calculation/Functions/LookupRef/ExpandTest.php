<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PHPUnit\Framework\Attributes\DataProvider;

class ExpandTest extends AllSetupTeardown
{
    #[DataProvider('providerExpand')]
    public function testExpand(mixed $expectedResult, string $formula): void
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

    public static function providerExpand(): array
    {
        return require 'tests/data/Calculation/LookupRef/EXPAND.php';
    }
}

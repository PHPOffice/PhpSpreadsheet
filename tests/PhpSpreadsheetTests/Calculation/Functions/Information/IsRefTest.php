<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef\AllSetupTeardown;
use PHPUnit\Framework\Attributes\DataProvider;

class IsRefTest extends AllSetupTeardown
{
    #[DataProvider('providerIsRef')]
    public function testIsRef(mixed $expected, string $ref): void
    {
        if ($expected === 'incomplete') {
            self::markTestIncomplete('Calculation is too complicated');
        }
        $sheet = $this->getSheet();

        $sheet->getParentOrThrow()->addDefinedName(new NamedRange('NAMED_RANGE', $sheet, 'C1'));
        $sheet->getCell('A1')->setValue("=ISREF($ref)");
        self::assertSame($expected, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerIsRef(): array
    {
        return [
            'cell reference' => [true, 'B1'],
            'invalid cell reference' => [false, 'ZZZ1'],
            'cell range' => [true, 'B1:B2'],
            'complex cell range' => [true, 'B1:D4 C1:C5'],
            'text string' => [false, '"PHP"'],
            'math expression' => [false, 'B1*B2'],
            'unquoted sheet name' => [true, 'Worksheet2!B1'],
            'quoted sheet name' => [true, "'Worksheet2'!B1:B2"],
            'quoted sheet name with apostrophe' => [true, "'Work''sheet2'!B1:B2"],
            'named range' => [true, 'NAMED_RANGE'],
            'unknown named range' => [false, 'xNAMED_RANGE'],
            'indirect to a cell reference' => [true, 'INDIRECT("A1")'],
            'indirect to a worksheet/cell reference' => [true, 'INDIRECT("\'Worksheet\'!A1")'],
            'indirect to invalid worksheet/cell reference' => [false, 'INDIRECT("\'Invalid Worksheet\'!A1")'],
            'returned cell reference' => ['incomplete', 'CHOOSE(2, A1, B1, C1)'],
        ];
    }
}

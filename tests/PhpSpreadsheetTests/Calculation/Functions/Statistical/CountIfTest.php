<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PHPUnit\Framework\Attributes\DataProvider;

class CountIfTest extends AllSetupTeardown
{
    #[DataProvider('providerCOUNTIF')]
    public function testCOUNTIF(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('COUNTIF', $expectedResult, ...$args);
    }

    public function testMultipleRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['apples', 'oranges', 'peaches', 'apples'],
            ['bananas', 'mangoes', 'grapes', 'cherries'],
        ]);
        $sheet->getCell('Z99')->setValue('=COUNTIF(A1:D2,"*p*e*")');
        self::assertSame(4, $sheet->getCell('Z99')->getCalculatedValue());
    }

    public static function providerCOUNTIF(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIF.php';
    }

    public function testOutliers(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=COUNTIF(5,"<32")');

        try {
            $sheet->getCell('A1')->getCalculatedValue();
            self::fail('Should receive exception for non-array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }

        $sheet->getCell('A4')->setValue('=COUNTIF(#REF!,1)');
        self::assertSame('#REF!', $sheet->getCell('A4')->getCalculatedValue());
    }
}

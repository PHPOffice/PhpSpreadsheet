<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;

class CountBlankTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTBLANK
     */
    public function testCOUNTBLANK(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('COUNTBLANK', $expectedResult, ...$args);
    }

    public static function providerCOUNTBLANK(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTBLANK.php';
    }

    public function testOutliers(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('C1')->setValue(1);
        $sheet->getCell('C2')->setValue(2);
        $sheet->getCell('C4')->setValue(4);
        $sheet->getCell('A1')->setValue('=COUNTBLANK(5)');

        try {
            $sheet->getCell('A1')->getCalculatedValue();
            self::fail('Should receive exception for non-array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }
        $sheet->getCell('A2')->setValue('=COUNTBLANK({1;2;4})');

        try {
            $sheet->getCell('A1')->getCalculatedValue();
            self::fail('Should receive exception for inline array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }
        $sheet->getCell('A3')->setValue('=COUNTBLANK(C1)');
        self::assertSame(0, $sheet->getCell('A3')->getCalculatedValue(), 'arg is single non-blank cell');
        $sheet->getCell('A4')->setValue('=COUNTBLANK(D2)');
        self::assertSame(1, $sheet->getCell('A4')->getCalculatedValue(), 'arg is single null cell');
        $sheet->getCell('A5')->setValue('=COUNTBLANK(D3:D4)');
        self::assertSame(2, $sheet->getCell('A5')->getCalculatedValue(), 'arg is two cells both null');
    }
}

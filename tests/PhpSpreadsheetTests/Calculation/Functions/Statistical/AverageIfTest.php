<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;

class AverageIfTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVERAGEIF
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIF($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('AVERAGEIF', $expectedResult, ...$args);
    }

    public static function providerAVERAGEIF(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEIF.php';
    }

    public function testOutliers(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('C1')->setValue(5);
        $sheet->getCell('A1')->setValue('=AVERAGEIF(5,"<32")');

        try {
            $sheet->getCell('A1')->getCalculatedValue();
            self::fail('Should receive exception for non-array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }
        $sheet->getCell('A2')->setValue('=AVERAGEIF({5},"<32")');

        try {
            $sheet->getCell('A2')->getCalculatedValue();
            self::fail('Should receive exception for literal array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }
        $sheet->getCell('A3')->setValue('=AVERAGEIF(C1,"<32")');
        self::assertSame(5, $sheet->getCell('A3')->getCalculatedValue(), 'first arg is single cell');
    }
}

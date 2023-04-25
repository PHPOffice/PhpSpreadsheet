<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;

class AverageIfsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAVERAGEIFS
     *
     * @param mixed $expectedResult
     */
    public function testAVERAGEIFS($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('AVERAGEIFS', $expectedResult, ...$args);
    }

    public static function providerAVERAGEIFS(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEIFS.php';
    }

    // For example, A1=75, A2=94, A3=86:
    // =AVERAGEIFS(A1:A3,A1:A3,">80") gives an answer, but
    // =AVERAGEIFS({75;94;86},{75;94;86},">80") does not.
    public function testOutliers(): void
    {
        $sheet = $this->getSheet();
        $this->setCell('A1', 75);
        $this->setCell('A2', 94);
        $this->setCell('A3', 86);
        $sheet->getCell('C1')->setValue('=AVERAGEIFS(A1:A3,A1:A3,">80")');
        self::assertSame(90, $sheet->getCell('C1')->getCalculatedValue(), 'first and second args are range');
        $sheet->getCell('C2')->setValue('=AVERAGEIFS({75;94;86},A1:A3,">80")');

        try {
            $sheet->getCell('C2')->getCalculatedValue();
            self::fail('Should receive exception for literal array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage(), 'first arg is array literal');
        }
        $sheet->getCell('C3')->setValue('=AVERAGEIFS(A1:A3,{75;94;86},">80")');

        try {
            $sheet->getCell('C3')->getCalculatedValue();
            self::fail('Should receive exception for literal array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage(), 'second arg is array literal');
        }
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PHPUnit\Framework\Attributes\DataProvider;

class AverageIfTest extends AllSetupTeardown
{
    #[DataProvider('providerAVERAGEIF')]
    public function testAVERAGEIF(mixed $expectedResult, mixed ...$args): void
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
        $sheet->getCell('A3')->setValue('=AVERAGEIF(C1:C1,"<32")');
        self::assertSame(5, $sheet->getCell('A3')->getCalculatedValue(), 'first arg is single cell');

        $sheet->getCell('A4')->setValue('=AVERAGEIF(#REF!,1)');
        self::assertSame('#REF!', $sheet->getCell('A4')->getCalculatedValue());

        $sheet->getCell('A5')->setValue('=AVERAGEIF(D1:D4, 1, #REF!)');
        self::assertSame('#REF!', $sheet->getCell('A5')->getCalculatedValue());
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PHPUnit\Framework\Attributes\DataProvider;

class SumIfTest extends AllSetupTeardown
{
    /**
     * @param mixed[] $array1
     * @param null|mixed[] $array2
     */
    #[DataProvider('providerSUMIF')]
    public function testSUMIF2(mixed $expectedResult, array $array1, mixed $condition, ?array $array2 = null): void
    {
        $this->mightHaveException($expectedResult);
        if ($expectedResult === 'incomplete') {
            self::markTestIncomplete('Raises formula error - researching solution');
        }
        $this->setArrayAsValue();
        $sheet = $this->getSheet();
        $sheet->fromArray($array1, null, 'A1', true);
        $maxARow = count($array1);
        $firstArg = "A1:A$maxARow";
        $this->setCell('B1', $condition);
        $secondArg = 'B1';
        if (empty($array2)) {
            $sheet->getCell('D1')->setValue("=SUMIF($firstArg, $secondArg)");
        } else {
            $sheet->fromArray($array2, null, 'C1', true);
            $maxCRow = count($array2);
            $thirdArg = "C1:C$maxCRow";
            $sheet->getCell('D1')->setValue("=SUMIF($firstArg, $secondArg, $thirdArg)");
        }
        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMIF(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMIF.php';
    }

    public function testOutliers(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('=SUMIF(5,"<32")');

        try {
            $sheet->getCell('A1')->getCalculatedValue();
            self::fail('Should receive exception for non-array arg');
        } catch (CalcException $e) {
            self::assertStringContainsString('Must specify range of cells', $e->getMessage());
        }

        $sheet->getCell('A4')->setValue('=SUMIF(#REF!,"<32")');
        self::assertSame('#REF!', $sheet->getCell('A4')->getCalculatedValue());
        $sheet->getCell('A5')->setValue('=SUMIF(D1:D4, 1, #REF!)');
        self::assertSame('#REF!', $sheet->getCell('A5')->getCalculatedValue());
    }
}

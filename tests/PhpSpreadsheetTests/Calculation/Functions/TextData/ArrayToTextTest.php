<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PHPUnit\Framework\Attributes\DataProvider;

class ArrayToTextTest extends AllSetupTeardown
{
    /** @param mixed[] $testData */
    #[DataProvider('providerARRAYTOTEXT')]
    public function testArrayToText(string $expectedResult, array $testData, int $mode): void
    {
        $worksheet = $this->getSheet();
        $worksheet->fromArray($testData, null, 'A1', true);
        $worksheet->getCell('H1')->setValue("=ARRAYTOTEXT(A1:C5, {$mode})");

        $result = $worksheet->getCell('H1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerARRAYTOTEXT(): array
    {
        return require 'tests/data/Calculation/TextData/ARRAYTOTEXT.php';
    }
}

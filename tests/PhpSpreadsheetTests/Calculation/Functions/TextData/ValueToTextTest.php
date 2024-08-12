<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class ValueToTextTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerVALUE
     */
    public function testVALUETOTEXT(mixed $expectedResult, mixed $value, int|string $format): void
    {
        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $sheet->getCell('B1')->setValue("=VALUETOTEXT(A1, {$format})");

        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerVALUE(): array
    {
        return require 'tests/data/Calculation/TextData/VALUETOTEXT.php';
    }

    // In Spreadsheet context, never see cell value as RichText.
    //    It will use calculatedValue, which is a string.
    // Add an additional test for that condition.
    public function testRichText(): void
    {
        $richText1 = new RichText();
        $richText1->createTextRun('Hello');
        $richText1->createText(' World');
        self::assertSame('Hello World', Format::valueToText($richText1, 0));
    }
}

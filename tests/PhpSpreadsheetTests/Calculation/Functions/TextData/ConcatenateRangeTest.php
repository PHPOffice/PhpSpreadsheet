<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ConcatenateRangeTest extends AllSetupTeardown
{
    private string $arrayReturnType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arrayReturnType = Calculation::getArrayReturnType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Calculation::setArrayReturnType($this->arrayReturnType);
    }

    public function testIssue4061(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('a');
        $sheet->getCell('A2')->setValue('b');
        $sheet->getCell('A3')->setValue('c');
        $sheet->getCell('C1')->setValue('1');
        $sheet->getCell('C2')->setValue('2');
        $sheet->getCell('C3')->setValue('3');
        $sheet->getCell('B1')->setValue('=CONCATENATE(A1:A3, "-", C1:C3)');
        self::assertSame('a-1', $sheet->getCell('B1')->getCalculatedValue());
        $sheet->getCell('X1')->setValue('=A1:A3&"-"&C1:C3');
        self::assertSame('a-1', $sheet->getCell('X1')->getCalculatedValue());
        $sheet->getCell('D1')->setValue('=CONCAT(A1:A3, "-", C1:C3)');
        self::assertSame('abc-123', $sheet->getCell('D1')->getCalculatedValue());
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet->getCell('E1')->setValue('=CONCATENATE(A1:A3, "-", C1:C3)');
        self::assertSame([['a-1'], ['b-2'], ['c-3']], $sheet->getCell('E1')->getCalculatedValue());
        $sheet->getCell('Y1')->setValue('=A1:A3&"-"&C1:C3');
        self::assertSame([['a-1'], ['b-2'], ['c-3']], $sheet->getCell('Y1')->getCalculatedValue());
        $sheet->getCell('F1')->setValue('=CONCAT(A1:A3, "-", C1:C3)');
        self::assertSame('abc-123', $sheet->getCell('F1')->getCalculatedValue());
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;

class IndirectTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerINDIRECT
     */
    public function testINDIRECT(mixed $expectedResult, mixed $cellReference = 'omitted', mixed $a1 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue(100);
        $sheet->getCell('A2')->setValue(200);
        $sheet->getCell('A3')->setValue(300);
        $sheet->getCell('A4')->setValue(400);
        $sheet->getCell('A5')->setValue(500);
        $sheet->setTitle('ThisSheet');

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->getCell('A1')->setValue(10);
        $sheet1->getCell('A2')->setValue(20);
        $sheet1->getCell('A3')->setValue(30);
        $sheet1->getCell('A4')->setValue(40);
        $sheet1->getCell('A5')->setValue(50);
        $sheet1->getCell('B1')->setValue(1);
        $sheet1->getCell('B2')->setValue(2);
        $sheet1->getCell('B3')->setValue(3);
        $sheet1->getCell('B4')->setValue(4);
        $sheet1->getCell('B5')->setValue(5);
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('newnr', $sheet1, '$A$2:$A$4'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('localname', $sheet1, '$B$2:$B$4', true));

        $this->setCell('B1', $cellReference);
        $this->setCell('B2', $a1);
        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT())');
        } elseif ($a1 === 'omitted') {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT(B1))');
        } else {
            $sheet->getCell('B3')->setValue('=SUM(INDIRECT(B1, B2))');
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerINDIRECT(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDIRECT.php';
    }

    public function testINDIRECTEurUsd(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('EUR');
        $sheet->getCell('A2')->setValue('USD');
        $sheet->getCell('B1')->setValue(360);
        $sheet->getCell('B2')->setValue(300);

        $this->getSpreadsheet()->addNamedRange(new NamedRange('EUR', $sheet, '$B$1'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('USD', $sheet, '$B$2'));

        $this->setCell('E1', '=INDIRECT("USD")');

        $result = $sheet->getCell('E1')->getCalculatedValue();
        self::assertSame(300, $result);
    }

    public function testINDIRECTLeadingEquals(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('EUR');
        $sheet->getCell('A2')->setValue('USD');
        $sheet->getCell('B1')->setValue(360);
        $sheet->getCell('B2')->setValue(300);

        $this->getSpreadsheet()->addNamedRange(new NamedRange('EUR', $sheet, '=$B$1'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('USD', $sheet, '=$B$2'));

        $this->setCell('E1', '=INDIRECT("USD")');

        $result = $sheet->getCell('E1')->getCalculatedValue();
        self::assertSame(300, $result);
    }

    public function testIndirectFile1(): void
    {
        $reader = new ReaderXlsx();
        $file = 'tests/data/Calculation/LookupRef/IndirectDefinedName.xlsx';
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $result = $sheet->getCell('A5')->getCalculatedValue();
        self::assertSame(80, $result);
        $value = $sheet->getCell('A5')->getValue();
        self::assertSame('=INDIRECT("CURRENCY_EUR")', $value);
    }

    public function testIndirectFile2(): void
    {
        $reader = new ReaderXlsx();
        $file = 'tests/data/Calculation/LookupRef/IndirectFormulaSelection.xlsx';
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $result = $sheet->getCell('A5')->getCalculatedValue();
        self::assertSame(100, $result);
        $value = $sheet->getCell('A5')->getValue();
        self::assertSame('=CURRENCY_SELECTOR', $value);
        $formula = $spreadsheet->getNamedFormula('CURRENCY_SELECTOR');
        if ($formula === null) {
            self::fail('Expected named formula was not defined');
        } else {
            self::assertSame('INDIRECT("CURRENCY_"&Sheet1!$D$1)', $formula->getFormula());
        }
    }

    public function testDeprecatedCall(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('A2');
        $sheet->getCell('A2')->setValue('This is it');
        $result = \PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Indirect::INDIRECT('A2', true, $sheet->getCell('A1'));
        $result = \PhpOffice\PhpSpreadsheet\Calculation\Functions::flattenSingleValue($result);
        self::assertSame('This is it', $result);
    }

    /**
     * @dataProvider providerRelative
     */
    public function testR1C1Relative(string|int|null $expectedResult, string $address): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['a1', 'b1', 'c1'],
            ['a2', 'b2', 'c2'],
            ['a3', 'b3', 'c3'],
            ['a4', 'b4', 'c4'],
        ]);
        $sheet->getCell('B2')->setValue('=INDIRECT("' . $address . '", false)');
        self::assertSame($expectedResult, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerRelative(): array
    {
        return [
            'same row with bracket next column' => ['c2', 'R[]C[+1]'],
            'same row without bracket next column' => ['c2', 'RC[+1]'],
            'same row without bracket next column no plus sign' => ['c2', 'RC[1]'],
            'same row previous column' => ['a2', 'RC[-1]'],
            'previous row previous column' => ['a1', 'R[-1]C[-1]'],
            'previous row same column with bracket' => ['b1', 'R[-1]C[]'],
            'previous row same column without bracket' => ['b1', 'R[-1]C'],
            'previous row next column' => ['c1', 'R[-1]C[+1]'],
            'next row no plus sign previous column' => ['a3', 'R[1]C[-1]'],
            'next row previous column' => ['a3', 'R[+1]C[-1]'],
            'next row same column' => ['b3', 'R[+1]C'],
            'next row next column' => ['c3', 'R[+1]C[+1]'],
            'two rows down same column' => ['b4', 'R[+2]C'],
            'invalid row' => ['#REF!', 'R[-2]C'],
            'invalid column' => ['#REF!', 'RC[-2]'],
            'circular reference' => [0, 'RC'], // matches Excel's treatment
            'absolute row absolute column' => ['c2', 'R2C3'],
            'absolute row relative column' => ['a2', 'R2C[-1]'],
            'relative row absolute column lowercase' => ['a2', 'rc1'],
            'uninitialized cell' => [0, 'RC[+2]'], // Excel result is 0, PhpSpreadsheet was null
        ];
    }

    private static bool $definedFormulaWorking = false;

    public function testAboveCell(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->addNamedFormula(
            new NamedFormula('SumAbove', $spreadsheet->getActiveSheet(), '=SUM(INDIRECT(ADDRESS(1,COLUMN())):INDIRECT(ADDRESS(ROW()-1,COLUMN())))')
        );
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue(100);
        $sheet->getCell('A2')->setValue(200);
        $sheet->getCell('A3')->setValue(300);
        $sheet->getCell('A4')->setValue(400);
        $sheet->getCell('A5')->setValue(500);
        $sheet->getCell('A6')->setValue('=SUM(A$1:INDIRECT(ADDRESS(ROW()-1,COLUMN())))');
        self::AssertSame(1500, $sheet->getCell('A6')->getCalculatedValue());
        if (self::$definedFormulaWorking) {
            $sheet->getCell('B1')->setValue(10);
            $sheet->getCell('B2')->setValue(20);
            $sheet->getCell('B3')->setValue(30);
            $sheet->getCell('B4')->setValue(40);
            $sheet->getCell('B5')->setValue('=SumAbove');
            self::AssertSame(100, $sheet->getCell('B5')->getCalculatedValue());
        } else {
            self::markTestIncomplete('PhpSpreadsheet does not handle this correctly');
        }
    }
}

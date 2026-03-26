<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PHPUnit\Framework\Attributes\DataProvider;

class XLookupTest extends AllSetupTeardown
{
    #[DataProvider('providerXLOOKUP')]
    public function testXLOOKUP(mixed $expectedResult, string $formula): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry'],
            [1.20, 0.50, 3.00, 2.50, 5.00],
            ['A01', 'B02', 'C03', 'D04', 'E05'],
            [100, 250, 30, 80, 15],
        ]);
        $sheet->fromArray([
            ['Alice', 'Bob', 'Alice', 'Carol'],
            [88, 72, 95, 81],
        ], null, 'H1');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('names1', $sheet, '$H$1:$K$1'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('values1', $sheet, '$H$2:$K$2'));
        $sheet->fromArray([
            [0, 10000, 50000, 100000, 500000],
            [0.00, 0.10, 0.20, 0.30, 0.40],
        ], null, 'H10');
        $sheet->fromArray([
            [6, 7, 8, 9, 10],
            ['XS', 'S', 'M', 'L', 'XL'],
        ], null, 'A14');
        $sheet->fromArray([
            [101, 204, 317, 489, 562, 741, 890],
            ['Grace', 'Alice', 'Bob', 'Carol', 'Dave', 'Eve', 'Frank'],
        ], null, 'H15');
        $sheet->fromArray([
            [9101, 8204, 7317, 6489, 5562, 4741, 3890],
            ['Grace', 'Alice', 'Bob', 'Carol', 'Dave', 'Eve', 'Frank'],
        ], null, 'H25');

        $sheet->getCell('Z98')->setValue('Cherry');
        $sheet->getCell('Z99')->setValue($formula);
        $result = $sheet->getCell('Z99')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerXLOOKUP(): array
    {
        return [
            'simple lookup' => [3.0, '=XLOOKUP("Cherry", A1:E1, A2:E2)'],
            'simple lookup cell' => [3.0, '=XLOOKUP(Z98, A1:E1, A2:E2)'],
            'not found with string' => ['Not found', '=XLOOKUP("Fig", A1:E1, A2:E2, "Not found")'],
            'not found with default string' => ['#N/A!', '=XLOOKUP("Fig", A1:E1, A2:E2)'],
            'Wildcard match (case-insensitive)' => ['E05', '=XLOOKUP("EL*", A1:E1, A3:E3, "Not found", 2)'],
            'Wildcard with ?' => [80, '=XLOOKUP("?ate", A1:E1, A4:E4, "Not found", 2)'],
            'Array sizes do not match' => ['#VALUE!', '=XLOOKUP("Apple", A1:E1, A2:D2)'],
            'Invalid match mode' => ['#VALUE!', '=XLOOKUP("Apple", A1:E1, A2:E2, "Not found", 4)'],
            'Invalid search mode' => ['#VALUE!', '=XLOOKUP("Apple", A1:E1, A2:E2, "Not found", 0, 5)'],
            'Arrays supplied as scalar' => [1.2, '=XLOOKUP("Apple", "Apple", A2)'],
            'Arrays supplied as scalar non-match' => ['#N/A!', '=XLOOKUP("Apple", "Banana", A2)'],
            'Reverse search with defined names' => [95, '=XLOOKUP("Alice", names1, values1, "Not found", 0, -1)'],
            'Next smaller approximate match' => [0.2, '=XLOOKUP(75000, H10:L10, H11:L11, "Not found", -1)'],
            'Next larger approximate match' => ['L', '=XLOOKUP(8.5, A14:E14, A15:E15, "Not found", 1)'],
            'Binary search' => ['Carol', '=XLOOKUP(489, H15:N15, H16:N16, "Not found", 0, 2)'],
            'Binary search fail' => ['Not found', '=XLOOKUP(490, H15:N15, H16:N16, "Not found", 0, 2)'],
            'Descending Binary search' => ['Bob', '=XLOOKUP(7317, H25:N25, H26:N26, "Not found", 0, -2)'],
        ];
    }

    public function testHorizontal(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Product', 'Laptop', 'Headphone', 'Watch', 'TV'],
            ['ID', 1, 2, 3, 4],
            ['Price', 15.00, 25.00, 30.00, 20.00],
        ], null, 'A2');
        $sheet->setCellValue('E8', 'Headphone');
        $sheet->setCellValue('E9', '=XLOOKUP(E8, B2:E2, B4:E4)');
        self::assertSame(25.0, $sheet->getCell('E9')->getCalculatedValue());
    }

    public function testBackwardsDirection(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Grades', 'Student Name'],
            [85, 'Drake'],
            [56, 'Robin'],
            [95, 'Raven'],
            [84, 'Sam'],
            [91, 'Dale'],
            [81, 'John'],
        ]);
        $sheet->setCellValue('D5', 'Robin');
        $sheet->setCellValue('D6', '=XLOOKUP(D5, B2:B7, A2:A7)');
        self::assertSame(56, $sheet->getCell('D6')->getCalculatedValue());
    }

    public function testArray(): void
    {
        $this->getSpreadsheet()->returnArrayAsArray();
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Red', 4.14],
            ['Orange', 4.19],
            ['Yellow', 5.17],
            ['Green', 5.77],
            ['Blue', 6.39],
        ], null, 'K21');
        $sheet->getCell('A23')
            ->setValue('=XLOOKUP({"Red","Orange","Green"}, K21:K25, L21:L25)');
        self::assertSame([4.14, 4.19, 5.77], $sheet->getCell('A23')->getCalculatedValue());
    }

    public function testMultiple(): void
    {
        $this->getSpreadsheet()->returnArrayAsArray();
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['ID', 'Name', 'Department', 'Salary'],
            [1001, 'Darwin', 'HR', 89000],
            [1002, 'Sam', 'IT', 74000],
            [1003, 'Robin', 'Finance', 59000],
            [1004, 'Raven', 'Marketing', 44000],
            [1005, 'Johnny', 'IT', 29000],
            [1006, 'Tom', 'Marketing', 14000],
            [1007, 'Taylor', 'Finance', 84000],
            [1008, 'Frank', 'HR', 96000],
            [1009, 'George', 'Sales', 21000],
        ]);
        $sheet->getCell('F5')->setValue(1004);
        $sheet->getCell('F6')
            ->setValue('=XLOOKUP(F5, A2:A10, B2:D10)');
        self::assertSame(
            ['Raven', 'Marketing', 44000],
            $sheet->getCell('F6')->getCalculatedValue()
        );
    }
}

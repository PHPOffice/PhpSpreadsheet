<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Issue4696Test extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    #[DataProvider('providerIsDateTime')]
    public function testIsDateTime(bool $expectedResult, string $expectedFormatted, int|float|string $value): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        if (is_string($value) && $value[0] !== '=') {
            $sheet->getCell('A1')->setValueExplicit($value, DataType::TYPE_STRING);
        } else {
            $sheet->getCell('A1')->setValue($value);
        }
        $sheet->getStyle('A1')->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        self::assertSame(
            $expectedResult,
            Date::isDateTime($sheet->getCell('A1'))
        );
        self::assertSame(
            $expectedFormatted,
            $sheet->getCell('A1')->getFormattedValue()
        );
    }

    public static function providerIsDateTime(): array
    {
        return [
            'valid integer' => [true, '1903-12-31', 1461],
            'valid integer stored as string' => [true, '1904-01-01', '1462'],
            'valid integer stored as concatenated string' => [true, '1904-01-01', '="14"&"62"'],
            'valid float' => [true, '1903-12-31', 1461.5],
            'valid float stored as string' => [true, '1903-12-31', '1461.5'],
            'out-of-range integer' => [false, '7000989091802000122', 7000989091802000122],
            'out-of-range float' => [false, '7.000989091802E+18', 7000989091802000122.1],
            'out-of-range float stored as string' => [false, '7000989091802000122.1', '7000989091802000122.1'],
            'non-numeric' => [false, 'xyz', 'xyz'],
            'issue 917' => [false, '5e8630b8-603c-43fe-b038-6154a3f893ab', '5e8630b8-603c-43fe-b038-6154a3f893ab'],
        ];
    }

    #[DataProvider('providerOtherFunctions')]
    public function testOtherFunctions(string $function): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(7000989091802000122);
        $sheet->getCell('A3')->setValue(39107); // 2007-01-25
        $sheet->getCell('A4')->setValue(39767); // 2008-11-15
        $sheet->getCell('A5')->setValue(2);
        $sheet->getCell('A6')->setValue(1);
        $sheet->getCell('B1')->setValue($function);
        self::assertSame(
            '#NUM!',
            $sheet->getCell('B1')->getFormattedValue()
        );
    }

    public static function providerOtherFunctions(): array
    {
        return [
            ['=YEAR(A1)'],
            ['=MONTH(A1)'],
            ['=DAY(A1)'],
            ['=DAYS(A1,A1)'],
            ['=DAYS360(A1,A1)'],
            ['=DATEDIF(A1,A1,"D")'],
            ['=HOUR(A1)'],
            ['=MINUTE(A1)'],
            ['=SECOND(A1)'],
            ['=WEEKNUM(A1)'],
            ['=ISOWEEKNUM(A1)'],
            ['=WEEKDAY(A1)'],
            ['=COUPDAYBS(A1,A4,A5,A6)'],
            ['=COUPDAYS(A3,A2,A5,A6)'],
            ['=COUPDAYSNC(A3,A2,A5,A6)'],
            ['=COUPNCD(A3,A2,A5,A6)'],
            ['=COUPNUM(A3,A2,A5,A6)'],
            ['=COUPPCD(A3,A2,A5,A6)'],
        ];
    }
}

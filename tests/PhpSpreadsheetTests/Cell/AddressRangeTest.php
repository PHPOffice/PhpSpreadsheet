<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class AddressRangeTest extends TestCase
{
    /** @param class-string $className */
    #[DataProvider('providerMaxRowsAndColumns')]
    public function testConstants(mixed $expectedValue, string $className, string $constantName): void
    {
        $reflectionClass = new ReflectionClass($className);
        $result = $reflectionClass->getConstant($constantName);
        self::assertSame($expectedValue, $result);
    }

    /** return array<array{mixed, class-string, string}> */
    public static function providerMaxRowsAndColumns(): array
    {
        return [
            [1048576, AddressRange::class, 'MAX_ROW'],
            ['XFD', AddressRange::class, 'MAX_COLUMN'],
            [16384, AddressRange::class, 'MAX_COLUMN_INT'],
            [16384, AddressRange::class, 'MAX_ROW_XLS_OLD'],
            [65536, AddressRange::class, 'MAX_ROW_XLS'],
            ['IV', AddressRange::class, 'MAX_COLUMN_XLS'],
            [256, AddressRange::class, 'MAX_COLUMN_INT_XLS'],
            ['/^[A-Z]+1:[A-Z]+1048576$/', Style::class, 'REGEX_WHOLE_COLUMN'],
            ['/^A\d+:XFD\d+$/', Style::class, 'REGEX_WHOLE_ROW'],
            ['/^([A-Z]+1\:[A-Z]+)(16384|65536)$/', Xls::class, 'REGEX_WHOLE_COLUMN'],
            ['${1}1048576', Xls::class, 'REGEX_WHOLE_COLUMN_REPLACE'],
            ['/^(A\d+\:)IV(\d+)$/', Xls::class, 'REGEX_WHOLE_ROW'],
            ['${1}XFD${2}', Xls::class, 'REGEX_WHOLE_ROW_REPLACE'],
        ];
    }

    #[DataProvider('providerAddressLimits')]
    public function testWriterXlsParser(bool $expected, string $cellAddress): void
    {
        $reflectionMethod = new ReflectionMethod(Parser::class, 'cellToPackedRowCol');
        $spreadsheet = new Spreadsheet();
        $reflectionClass = new Parser($spreadsheet);

        try {
            $reflectionMethod->invokeArgs($reflectionClass, [$cellAddress]);
            $result = true;
        } catch (WriterException) {
            $result = false;
        }

        $spreadsheet->disconnectWorksheets();
        self::assertSame($expected, $result);
    }

    /** return array<array{bool, string}> */
    public static function providerAddressLimits(): array
    {
        return [
            'last possible row' => [true, 'A65536'],
            'beyond last possible row' => [false, 'A65537'],
            'last possible column' => [true, 'IV1'],
            'beyond last possible column' => [false, 'IW1'],
        ];
    }
}

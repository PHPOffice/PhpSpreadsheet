<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx\FunctionPrefix;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FunctionPrefixTest extends TestCase
{
    #[DataProvider('functionPrefixProvider')]
    public function testFunctionPrefix(string $expectedResult, string $functionString): void
    {
        $result = FunctionPrefix::addFunctionPrefix($functionString);
        self::assertSame($expectedResult, $result);
        $result = FunctionPrefix::addFunctionPrefixStripEquals("=$functionString");
        self::assertSame($expectedResult, $result);
    }

    /** @return array<string, array<int, string>> */
    public static function functionPrefixProvider(): array
    {
        return [
            'Basic Legacy Function' => ['SUM()', 'SUM()'],
            'New Function without Prefix' => ['_xlfn.ARABIC()', 'ARABIC()'],
            'New Function already Prefixed' => ['_xlfn.ARABIC()', '_xlfn.ARABIC()'],
            'New Function requiring Double-Prefix' => ['_xlfn._xlws.FILTER()', 'FILTER()'],
            'New Function requiring Double-Prefix already partially Prefixed' => ['_xlfn._xlws.FILTER()', '_xlfn.FILTER()'],
            'New Function requiring Double-Prefix already partially Prefixed #2' => ['_xlfn._xlws.FILTER()', '_xlws.FILTER()'],
            'New Function requiring Double-Prefix already Fully Prefixed' => ['_xlfn._xlws.FILTER()', '_xlfn._xlws.FILTER()'],
            'Multiple Functions' => ['_xlfn._xlws.SORT(_xlfn._xlws.FILTER(A:A, A:A<>""))', 'SORT(FILTER(A:A, A:A<>""))'],
            'DAYS/NETWORKDAYS 1' => ['_xlfn.DAYS(DATE(2023,1,1),TODAY())', 'DAYS(DATE(2023,1,1),TODAY())'],
            'DAYS/NETWORKDAYS 2' => ['_xlfn.DAYS(DATE(2023,1,1),TODAY())', '_xlfn.DAYS(DATE(2023,1,1),TODAY())'],
            'DAYS/NETWORKDAYS 3' => ['ABS(_xlfn.DAYS(DATE(2023,1,1),TODAY()))', 'ABS(DAYS(DATE(2023,1,1),TODAY()))'],
            'DAYS/NETWORKDAYS 4' => ['ABS(_xlfn.DAYS(DATE(2023,1,1),TODAY()))', 'ABS(_xlfn.DAYS(DATE(2023,1,1),TODAY()))'],
            'DAYS/NETWORKDAYS 5' => ['NETWORKDAYS(DATE(2023,1,1),TODAY(), C:C)', 'NETWORKDAYS(DATE(2023,1,1),TODAY(), C:C)'],
            'COUNTIFS reclassified as Legacy' => ['COUNTIFS()', 'COUNTIFS()'],
            'SUMIFS reclassified as Legacy' => ['SUMIFS()', 'SUMIFS()'],
            'BASE improperly classified by MS' => ['_xlfn.BASE()', 'BASE()'],
        ];
    }
}

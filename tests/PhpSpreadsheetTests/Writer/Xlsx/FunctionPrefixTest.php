<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx\FunctionPrefix;
use PHPUnit\Framework\TestCase;

class FunctionPrefixTest extends TestCase
{
    /**
     * @dataProvider functionPrefixProvider
     */
    public function testFunctionPrefix(string $expectedResult, string $functionString): void
    {
        $result = FunctionPrefix::addFunctionPrefix($functionString);
        self::assertSame($expectedResult, $result);
    }

    public function functionPrefixProvider(): array
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
        ];
    }

//    /**
//     * @dataProvider functionPrefixWithEqualsProvider
//     */
//    public function testFunctionPrefixWithEquals(string $expectedResult, string $functionString): void
//    {
//        $result = FunctionPrefix::addFunctionPrefixStripEquals($functionString);
//        self::assertSame($expectedResult, $result);
//    }
//
//    public function functionPrefixWithEqualsProvider(): array
//    {
//        return [
//            'Basic Legacy Function' => ['SUM()', '=SUM()'],
//            'New Function without Prefix' => ['_xlfn.ARABIC()', '=ARABIC()'],
//            'New Function already Prefixed' => ['_xlfn.ARABIC()', '=_xlfn.ARABIC()'],
//            'New Function requiring Double-Prefix' => ['_xlfn._xlws.FILTER()', '=FILTER()'],
//            'New Function requiring Double-Prefix already partially Prefixed' => ['_xlfn._xlws.FILTER()', '=_xlfn.FILTER()'],
//            'New Function requiring Double-Prefix already partially Prefixed #2' => ['_xlfn._xlws.FILTER()', '=_xlws.FILTER()'],
//            'New Function requiring Double-Prefix already Fully Prefixed' => ['_xlfn._xlws.FILTER()', '=_xlfn._xlws.FILTER()'],
//        ];
//    }
}

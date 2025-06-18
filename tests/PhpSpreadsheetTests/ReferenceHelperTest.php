<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PHPUnit\Framework\TestCase;

class ReferenceHelperTest extends TestCase
{
    public function setUp()
    {
    }

    public function testColumnSort()
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        usort($columnBase, [ReferenceHelper::class, 'columnSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }

    public function testColumnReverseSort()
    {
        $columnBase = $columnExpectedResult = [
            'A', 'B', 'Z',
            'AA', 'AB', 'AZ',
            'BA', 'BB', 'BZ',
            'ZA', 'ZB', 'ZZ',
            'AAA', 'AAB', 'AAZ',
            'ABA', 'ABB', 'ABZ',
            'AZA', 'AZB', 'AZZ',
            'BAA', 'BAB', 'BAZ',
            'BBA', 'BBB', 'BBZ',
            'BZA', 'BZB', 'BZZ',
        ];
        shuffle($columnBase);
        $columnExpectedResult = array_reverse($columnExpectedResult);
        usort($columnBase, [ReferenceHelper::class, 'columnReverseSort']);
        foreach ($columnBase as $key => $value) {
            self::assertEquals($columnExpectedResult[$key], $value);
        }
    }
}

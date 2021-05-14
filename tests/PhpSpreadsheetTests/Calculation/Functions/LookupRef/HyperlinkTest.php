<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PHPUnit\Framework\TestCase;

class HyperlinkTest extends TestCase
{
    /**
     * @dataProvider providerHYPERLINK
     *
     * @param mixed $expectedResult
     * @param null|string $linkUrl
     * @param null|string $description
     */
    public function testHYPERLINK($expectedResult, $linkUrl, $description): void
    {
        $hyperlink = new Hyperlink();

        $cell = $this->getMockBuilder(Cell::class)
            ->onlyMethods(['getHyperlink'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getHyperlink')
            ->willReturn($hyperlink);

        $result = LookupRef::HYPERLINK($linkUrl, $description, $cell);
        if (!is_array($expectedResult)) {
            self::assertSame($expectedResult, $result);
        } else {
            self::assertSame($expectedResult[1], $result);
            self::assertSame($expectedResult[0], $hyperlink->getUrl());
            self::assertSame($expectedResult[1], $hyperlink->getTooltip());
        }
    }

    public function providerHYPERLINK(): array
    {
        return require 'tests/data/Calculation/LookupRef/HYPERLINK.php';
    }

    public function testHYPERLINKwithoutCell(): void
    {
        $result = LookupRef::HYPERLINK('https://phpspreadsheet.readthedocs.io/en/latest/', 'Read the Docs');
        self::assertSame(Functions::REF(), $result);
    }
}

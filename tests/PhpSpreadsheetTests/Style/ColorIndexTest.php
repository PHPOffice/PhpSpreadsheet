<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Styles;
use PHPUnit\Framework\TestCase;

class ColorIndexTest extends TestCase
{
    /**
     * @dataProvider providerColorIndexes
     */
    public function testColorIndex(string $expectedResult, string $xml, bool $background = false): void
    {
        $sxml = simplexml_load_string($xml);
        if ($sxml === false) {
            self::fail('Unable to parse xml');
        } else {
            $styles = new Styles();
            $result = $styles->readColor($sxml, $background);
            self::assertSame($expectedResult, $result);
        }
    }

    public static function providerColorIndexes(): array
    {
        return [
            'subtract 7 to return system color 4' => ['FF00FF00', '<fgColor indexed="11"/>'],
            'default foreground color when out of range' => ['FF000000', '<color indexed="81"/>'],
            'default background color when out of range' => ['FFFFFFFF', '<bgColor indexed="81"/>', true],
            'rgb specified' => ['FF123456', '<bgColor rgb="FF123456"/>', true],
        ];
    }
}

<?php
/**
 * User: all-lala
 * Date: 10/09/2017
 * Time: 10:43.
 */

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls\Workbook;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook;
use PHPUnit_Framework_TestCase;

class WorkbookTest extends PHPUnit_Framework_TestCase
{
    private $workbook;

    protected function setUp()
    {
        $spreadsheet = new Spreadsheet();
        $strTotal = 0;
        $strUnique = 0;
        $str_table = [];
        $colors = [];
        $parser = new Parser();

        $this->workbook = new Workbook($spreadsheet, $strTotal, $strUnique, $str_table, $colors, $parser);
    }

    protected function tearDown()
    {
        $this->workbook = null;
    }

    /**
     * @dataProvider providerAddColor
     *
     * @param mixed $expectedResult
     * @param mixed $testColors
     */
    public function testAddColor($testColors, $expectedResult)
    {
        $workbookReflection = new \ReflectionClass(Workbook::class);
        $methodAddColor = $workbookReflection->getMethod('addColor');
        $propertyPalette = $workbookReflection->getProperty('palette');
        $methodAddColor->setAccessible(true);
        $propertyPalette->setAccessible(true);

        foreach ($testColors as $testColor) {
            $methodAddColor->invoke($this->workbook, $testColor);
        }

        $palette = $propertyPalette->getValue($this->workbook);

        $this->assertEquals($expectedResult, $palette);
    }

    public function providerAddColor()
    {
        $this->setUp();

        $workbookReflection = new \ReflectionClass(Workbook::class);
        $propertyPalette = $workbookReflection->getProperty('palette');
        $propertyPalette->setAccessible(true);

        $palette = $propertyPalette->getValue($this->workbook);

        $newColor_1 = [0x00, 0x00, 0x01, 0x00];
        $newColor_2 = [0x00, 0x00, 0x02, 0x00];
        $newColor_3 = [0x00, 0x00, 0x03, 0x00];

        // Add one new color
        $paletteTestOne = $palette;
        $paletteTestOne[8] = $newColor_1;

        // Add one new color + one existing color after index 8
        $paletteTestTwo = $paletteTestOne;

        // Add one new color + one existing color before index 9
        $paletteTestThree = $paletteTestOne;
        $paletteTestThree[9] = $palette[8];

        // Add three new color
        $paletteTestFour = $palette;
        $paletteTestFour[8] = $newColor_1;
        $paletteTestFour[9] = $newColor_2;
        $paletteTestFour[10] = $newColor_3;

        // Add all existing color
        $colorsAdd = array_map('self::paletteToColor', $palette);
        $paletteTestFive = $palette;

        // Add new color after all existing color
        $colorsAddTwo = array_map('self::paletteToColor', $palette);
        array_push($colorsAddTwo, self::paletteToColor($newColor_1));
        $paletteTestSix = $palette;

        // Add one existing color
        $paletteTestSeven = $palette;

        // Add two existing color
        $paletteTestHeight = $palette;

        // Add last existing color and add one new color
        $keyPalette = array_keys($palette);
        $last = end($keyPalette);
        $lastColor = $this->paletteToColor($palette[$last]);
        $paletteTestNine = $palette;

        return [
            [[self::paletteToColor($newColor_1)], $paletteTestOne],
            [[self::paletteToColor($newColor_1), self::paletteToColor($palette[12])], $paletteTestTwo],
            [[self::paletteToColor($newColor_1), self::paletteToColor($palette[8])], $paletteTestThree],
            [[$this->paletteToColor($newColor_1), $this->paletteToColor($newColor_2), $this->paletteToColor($newColor_3)], $paletteTestFour],
            [$colorsAdd, $paletteTestFive],
            [$colorsAddTwo, $paletteTestSix],
            [[self::paletteToColor($palette[8])], $paletteTestSeven],
            [[self::paletteToColor($palette[25]), self::paletteToColor($palette[10])], $paletteTestHeight],
            [[$lastColor, self::paletteToColor($newColor_1)], $paletteTestNine],
        ];
    }

    /**
     * Change palette color to rgb string.
     *
     * @param array $palette palette color
     *
     * @return string rgb string
     */
    public static function paletteToColor($palette)
    {
        return self::right('00' . dechex((int) ($palette[0])), 2)
            . self::right('00' . dechex((int) ($palette[1])), 2)
            . self::right('00' . dechex((int) ($palette[2])), 2);
    }

    /**
     * Return n right character in string.
     *
     * @param string $value text tu get right character
     * @param int $nbchar number of char at right of string
     *
     * @return string
     */
    public static function right($value, $nbchar)
    {
        return mb_substr($value, mb_strlen($value) - $nbchar, $nbchar);
    }
}

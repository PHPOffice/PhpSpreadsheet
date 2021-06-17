<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class WorkbookTest extends TestCase
{
    /**
     * @var Workbook
     */
    private $workbook;

    protected function setUp(): void
    {
        $spreadsheet = new Spreadsheet();
        $strTotal = 0;
        $strUnique = 0;
        $str_table = [];
        $colors = [];
        $parser = new Parser($spreadsheet);

        $this->workbook = new Workbook($spreadsheet, $strTotal, $strUnique, $str_table, $colors, $parser);
    }

    /**
     * @dataProvider providerAddColor
     */
    public function testAddColor(array $testColors, array $expectedResult): void
    {
        $workbookReflection = new ReflectionClass(Workbook::class);
        $methodAddColor = $workbookReflection->getMethod('addColor');
        $propertyPalette = $workbookReflection->getProperty('palette');
        $methodAddColor->setAccessible(true);
        $propertyPalette->setAccessible(true);

        foreach ($testColors as $testColor) {
            $methodAddColor->invoke($this->workbook, $testColor);
        }

        $palette = $propertyPalette->getValue($this->workbook);

        self::assertEquals($expectedResult, $palette);
    }

    public function providerAddColor(): array
    {
        $this->setUp();

        $workbookReflection = new ReflectionClass(Workbook::class);
        $propertyPalette = $workbookReflection->getProperty('palette');
        $propertyPalette->setAccessible(true);

        $palette = $propertyPalette->getValue($this->workbook);

        $newColor1 = [0x00, 0x00, 0x01, 0x00];
        $newColor2 = [0x00, 0x00, 0x02, 0x00];
        $newColor3 = [0x00, 0x00, 0x03, 0x00];

        // Add one new color
        $paletteTestOne = $palette;
        $paletteTestOne[8] = $newColor1;

        // Add one new color + one existing color after index 8
        $paletteTestTwo = $paletteTestOne;

        // Add one new color + one existing color before index 9
        $paletteTestThree = $paletteTestOne;
        $paletteTestThree[9] = $palette[8];

        // Add three new color
        $paletteTestFour = $palette;
        $paletteTestFour[8] = $newColor1;
        $paletteTestFour[9] = $newColor2;
        $paletteTestFour[10] = $newColor3;

        // Add all existing color
        $colorsAdd = array_map([$this, 'paletteToColor'], $palette);
        $paletteTestFive = $palette;

        // Add new color after all existing color
        $colorsAddTwo = array_map([$this, 'paletteToColor'], $palette);
        $colorsAddTwo[] = $this->paletteToColor($newColor1);
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
            [[$this->paletteToColor($newColor1)], $paletteTestOne],
            [[$this->paletteToColor($newColor1), $this->paletteToColor($palette[12])], $paletteTestTwo],
            [[$this->paletteToColor($newColor1), $this->paletteToColor($palette[8])], $paletteTestThree],
            [[$this->paletteToColor($newColor1), $this->paletteToColor($newColor2), $this->paletteToColor($newColor3)], $paletteTestFour],
            [$colorsAdd, $paletteTestFive],
            [$colorsAddTwo, $paletteTestSix],
            [[$this->paletteToColor($palette[8])], $paletteTestSeven],
            [[$this->paletteToColor($palette[25]), $this->paletteToColor($palette[10])], $paletteTestHeight],
            [[$lastColor, $this->paletteToColor($newColor1)], $paletteTestNine],
        ];
    }

    /**
     * Change palette color to rgb string.
     *
     * @param array $palette palette color
     *
     * @return string rgb string
     */
    private function paletteToColor($palette)
    {
        return $this->right('00' . dechex((int) ($palette[0])), 2)
            . $this->right('00' . dechex((int) ($palette[1])), 2)
            . $this->right('00' . dechex((int) ($palette[2])), 2);
    }

    /**
     * Return n right character in string.
     *
     * @param string $value text to get right character
     * @param int $nbchar number of char at right of string
     *
     * @return string
     */
    private function right($value, $nbchar)
    {
        return mb_substr($value, mb_strlen($value) - $nbchar, $nbchar);
    }
}

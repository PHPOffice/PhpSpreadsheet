<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class WorkbookTest extends TestCase
{
    private Workbook $workbook;

    private ?Spreadsheet $spreadsheet = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function setUpWorkbook(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
        }
        $this->spreadsheet = $spreadsheet = new Spreadsheet();
        $strTotal = 0;
        $strUnique = 0;
        $str_table = [];
        $colors = [];
        $parser = new Parser($spreadsheet);

        $this->workbook = new Workbook($spreadsheet, $strTotal, $strUnique, $str_table, $colors, $parser);
    }

    /**
     * @param string[] $testColors
     * @param string[] $expectedResult
     */
    public function xtestAddColor(array $testColors, array $expectedResult): void
    {
        $workbookReflection = new ReflectionClass(Workbook::class);
        $methodAddColor = $workbookReflection->getMethod('addColor');
        $propertyPalette = $workbookReflection->getProperty('palette');

        foreach ($testColors as $testColor) {
            $methodAddColor->invoke($this->workbook, $testColor);
        }

        $palette = $propertyPalette->getValue($this->workbook);

        self::assertEquals($expectedResult, $palette);
    }

    public function testAddColor(): void
    {
        $i = 0;
        $arrayEntries = $this->arrayAddColor();
        while ($i < count($arrayEntries)) {
            /** @var string[] */
            $entry0 = $arrayEntries[$i][0];
            /** @var string[] */
            $entry1 = $arrayEntries[$i][1];
            $this->xtestAddColor($entry0, $entry1);
            ++$i;
            $arrayEntries = $this->arrayAddColor();
        }
    }

    /** @return array<int, array<int, array<mixed>>> */
    public function arrayAddColor(): array
    {
        $this->setUpWorkbook();

        $workbookReflection = new ReflectionClass(Workbook::class);
        $propertyPalette = $workbookReflection->getProperty('palette');

        $palette = $propertyPalette->getValue($this->workbook);
        self::assertIsArray($palette);

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
        self::assertIsArray($palette[8]);
        self::assertIsArray($palette[10]);
        self::assertIsArray($palette[12]);
        self::assertIsArray($palette[25]);
        self::assertIsArray($palette[$last]);
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
     * @param array<mixed, mixed> $palette
     */
    private function paletteToColor(array $palette): string
    {
        return $this->right('00' . self::dec2hex($palette[0]), 2)
            . $this->right('00' . self::dec2hex($palette[1]), 2)
            . $this->right('00' . self::dec2hex($palette[2]), 2);
    }

    private static function dec2hex(mixed $value): string
    {
        return is_numeric($value) ? dechex((int) $value) : '0';
    }

    /**
     * Return n right character in string.
     *
     * @param string $value text to get right character
     * @param int $nbchar number of char at right of string
     */
    private function right(string $value, int $nbchar): string
    {
        return mb_substr($value, mb_strlen($value) - $nbchar, $nbchar);
    }
}

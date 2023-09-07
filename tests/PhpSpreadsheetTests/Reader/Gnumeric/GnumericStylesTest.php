<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class GnumericStylesTest extends TestCase
{
    /**
     * @dataProvider providerBorderStyle
     */
    public function testBorderStyle(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['borderStyle'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testBorderStyleCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['borderStyle'];
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerBorderStyle();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "Borderstyle $key not tested");
        }
    }

    /**
     * @dataProvider providerfillType
     */
    public function testFillType(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['fillType'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testFillTypeCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['fillType'];
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerfillType();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "fillType $key not tested");
        }
    }

    /**
     * @dataProvider providerHorizontal
     */
    public function testHorizontal(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['horizontal'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testHorizontalCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['horizontal'];
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerHorizontal();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "horizontal $key not tested");
        }
    }

    /**
     * @dataProvider providerunderline
     */
    public function testUnderline(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['underline'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testUnderlineCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['underline'];
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerUnderline();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "underline $key not tested");
        }
    }

    /**
     * @dataProvider providerVertical
     */
    public function testVertical(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['vertical'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testVerticalCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['vertical'];
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerVertical();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "vertical $key not tested");
        }
    }

    /**
     * @dataProvider providerDataType
     */
    public function testDataType(string $style, string $expectedResult): void
    {
        $styles = Gnumeric::gnumericMappings();
        $borders = $styles['dataType'];
        self::assertEquals($expectedResult, $borders[$style]);
    }

    public function testDataTypeCoverage(): void
    {
        $styles = Gnumeric::gnumericMappings();
        $expected = $styles['dataType'];
        self::assertArrayNotHasKey('70', $expected);
        self::assertArrayNotHasKey('80', $expected);
        $covered = [];
        foreach ($expected as $key => $val) {
            $covered[$key] = 0;
        }
        $tests = $this->providerDataType();
        foreach ($tests as $test) {
            $covered[$test[0]] = 1;
        }
        foreach ($covered as $key => $val) {
            self::assertEquals(1, $val, "dataType $key not tested");
        }
    }

    public static function providerBorderStyle(): array
    {
        return [
            ['0', Border::BORDER_NONE],
            ['1', Border::BORDER_THIN],
            ['2', Border::BORDER_MEDIUM],
            ['3', Border::BORDER_SLANTDASHDOT],
            ['4', Border::BORDER_DASHED],
            ['5', Border::BORDER_THICK],
            ['6', Border::BORDER_DOUBLE],
            ['7', Border::BORDER_DOTTED],
            ['8', Border::BORDER_MEDIUMDASHED],
            ['9', Border::BORDER_DASHDOT],
            ['10', Border::BORDER_MEDIUMDASHDOT],
            ['11', Border::BORDER_DASHDOTDOT],
            ['12', Border::BORDER_MEDIUMDASHDOTDOT],
            ['13', Border::BORDER_MEDIUMDASHDOTDOT],
        ];
    }

    public static function providerFillType(): array
    {
        return [
            ['1', Fill::FILL_SOLID],
            ['2', Fill::FILL_PATTERN_DARKGRAY],
            ['3', Fill::FILL_PATTERN_MEDIUMGRAY],
            ['4', Fill::FILL_PATTERN_LIGHTGRAY],
            ['5', Fill::FILL_PATTERN_GRAY125],
            ['6', Fill::FILL_PATTERN_GRAY0625],
            ['7', Fill::FILL_PATTERN_DARKHORIZONTAL],
            ['8', Fill::FILL_PATTERN_DARKVERTICAL],
            ['9', Fill::FILL_PATTERN_DARKDOWN],
            ['10', Fill::FILL_PATTERN_DARKUP],
            ['11', Fill::FILL_PATTERN_DARKGRID],
            ['12', Fill::FILL_PATTERN_DARKTRELLIS],
            ['13', Fill::FILL_PATTERN_LIGHTHORIZONTAL],
            ['14', Fill::FILL_PATTERN_LIGHTVERTICAL],
            ['15', Fill::FILL_PATTERN_LIGHTUP],
            ['16', Fill::FILL_PATTERN_LIGHTDOWN],
            ['17', Fill::FILL_PATTERN_LIGHTGRID],
            ['18', Fill::FILL_PATTERN_LIGHTTRELLIS],
        ];
    }

    public static function providerHorizontal(): array
    {
        return [
            ['1', Alignment::HORIZONTAL_GENERAL],
            ['2', Alignment::HORIZONTAL_LEFT],
            ['4', Alignment::HORIZONTAL_RIGHT],
            ['8', Alignment::HORIZONTAL_CENTER],
            ['16', Alignment::HORIZONTAL_CENTER_CONTINUOUS],
            ['32', Alignment::HORIZONTAL_JUSTIFY],
            ['64', Alignment::HORIZONTAL_CENTER_CONTINUOUS],
        ];
    }

    public static function providerUnderline(): array
    {
        return [
            ['1', Font::UNDERLINE_SINGLE],
            ['2', Font::UNDERLINE_DOUBLE],
            ['3', Font::UNDERLINE_SINGLEACCOUNTING],
            ['4', Font::UNDERLINE_DOUBLEACCOUNTING],
        ];
    }

    public static function providerVertical(): array
    {
        return [
            ['1', Alignment::VERTICAL_TOP],
            ['2', Alignment::VERTICAL_BOTTOM],
            ['4', Alignment::VERTICAL_CENTER],
            ['8', Alignment::VERTICAL_JUSTIFY],
        ];
    }

    public static function providerDataType(): array
    {
        return [
            ['10', DataType::TYPE_NULL],
            ['20', DataType::TYPE_BOOL],
            ['30', DataType::TYPE_NUMERIC], // Integer doesn't exist in Excel
            ['40', DataType::TYPE_NUMERIC], // Float
            ['50', DataType::TYPE_ERROR],
            ['60', DataType::TYPE_STRING],
            //'70':        //    Cell Range
            //'80':        //    Array
        ];
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SSException;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class FontFileNameTest extends TestCase
{
    private const DEFAULT_DIRECTORY = 'tests/data/Shared/FakeFonts/Default';
    private const MAC_DIRECTORY = 'tests/data/Shared/FakeFonts/Mac';
    private const RECURSE_DIRECTORY = 'tests/data/Shared/FakeFonts/Recurse';

    private string $holdDirectory;

    private array $holdExtraFontArray;

    protected function setUp(): void
    {
        $this->holdDirectory = Font::getTrueTypeFontPath();
        $this->holdExtraFontArray = Font::getExtraFontArray();
        Font::setExtraFontArray([
            'Extra Font' => [
                'x' => 'extrafont.ttf',
                'xb' => 'extrafontbd.ttf',
                'xi' => 'extrafonti.ttf',
                'xbi' => 'extrafontbi.ttf',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Font::setTrueTypeFontPath($this->holdDirectory);
        Font::setExtraFontArray($this->holdExtraFontArray);
    }

    /**
     * @dataProvider providerDefault
     */
    public function testDefaultFilenames(string $expected, array $fontArray): void
    {
        if ($expected === 'exception') {
            $this->expectException(SSException::class);
            $this->expectExceptionMessage('TrueType Font file not found');
        }
        Font::setTrueTypeFontPath(self::DEFAULT_DIRECTORY);
        $font = (new StyleFont())->applyFromArray($fontArray);
        $result = Font::getTrueTypeFontFileFromFont($font);
        self::assertSame($expected, basename($result));
    }

    public static function providerDefault(): array
    {
        return [
            ['arial.ttf', ['name' => 'Arial']],
            ['arialbd.ttf', ['name' => 'Arial', 'bold' => true]],
            ['ariali.ttf', ['name' => 'Arial', 'italic' => true]],
            ['arialbi.ttf', ['name' => 'Arial', 'bold' => true, 'italic' => true]],
            ['cour.ttf', ['name' => 'Courier New']],
            ['courbd.ttf', ['name' => 'Courier New', 'bold' => true]],
            ['couri.ttf', ['name' => 'Courier New', 'italic' => true]],
            ['courbi.ttf', ['name' => 'Courier New', 'bold' => true, 'italic' => true]],
            ['impact.ttf', ['name' => 'Impact']],
            'no bold impact' => ['impact.ttf', ['name' => 'Impact', 'bold' => true]],
            'no italic impact' => ['impact.ttf', ['name' => 'Impact', 'italic' => true]],
            'no bold italic impact' => ['impact.ttf', ['name' => 'Impact', 'bold' => true, 'italic' => true]],
            ['tahoma.ttf', ['name' => 'Tahoma']],
            ['tahomabd.ttf', ['name' => 'Tahoma', 'bold' => true]],
            'no italic tahoma' => ['tahoma.ttf', ['name' => 'Tahoma', 'italic' => true]],
            'no bold italic tahoma' => ['tahomabd.ttf', ['name' => 'Tahoma', 'bold' => true, 'italic' => true]],
            'Times New Roman not in directory for this test' => ['exception', ['name' => 'Times New Roman']],
            ['extrafont.ttf', ['name' => 'Extra Font']],
            ['extrafontbd.ttf', ['name' => 'Extra Font', 'bold' => true]],
            ['extrafonti.ttf', ['name' => 'Extra Font', 'italic' => true]],
            ['extrafontbi.ttf', ['name' => 'Extra Font', 'bold' => true, 'italic' => true]],
        ];
    }

    /**
     * @dataProvider providerMac
     */
    public function testMacFilenames(string $expected, array $fontArray): void
    {
        if ($expected === 'exception') {
            $this->expectException(SSException::class);
            $this->expectExceptionMessage('TrueType Font file not found');
        }
        Font::setTrueTypeFontPath(self::MAC_DIRECTORY);
        $font = (new StyleFont())->applyFromArray($fontArray);
        $result = Font::getTrueTypeFontFileFromFont($font);
        self::assertSame($expected, ucfirst(basename($result))); // allow for Windows case-insensitivity
    }

    public static function providerMac(): array
    {
        return [
            ['Arial.ttf', ['name' => 'Arial']],
            ['Arial Bold.ttf', ['name' => 'Arial', 'bold' => true]],
            ['Arial Italic.ttf', ['name' => 'Arial', 'italic' => true]],
            ['Arial Bold Italic.ttf', ['name' => 'Arial', 'bold' => true, 'italic' => true]],
            ['Courier New.ttf', ['name' => 'Courier New']],
            ['Courier New Bold.ttf', ['name' => 'Courier New', 'bold' => true]],
            ['Courier New Italic.ttf', ['name' => 'Courier New', 'italic' => true]],
            ['Courier New Bold Italic.ttf', ['name' => 'Courier New', 'bold' => true, 'italic' => true]],
            ['Impact.ttf', ['name' => 'Impact']],
            'no bold impact' => ['Impact.ttf', ['name' => 'Impact', 'bold' => true]],
            'no italic impact' => ['Impact.ttf', ['name' => 'Impact', 'italic' => true]],
            'no bold italic impact' => ['Impact.ttf', ['name' => 'Impact', 'bold' => true, 'italic' => true]],
            ['Tahoma.ttf', ['name' => 'Tahoma']],
            ['Tahoma Bold.ttf', ['name' => 'Tahoma', 'bold' => true]],
            'no italic tahoma' => ['Tahoma.ttf', ['name' => 'Tahoma', 'italic' => true]],
            'no bold italic tahoma' => ['Tahoma Bold.ttf', ['name' => 'Tahoma', 'bold' => true, 'italic' => true]],
            'Times New Roman not in directory for this test' => ['exception', ['name' => 'Times New Roman']],
            ['Extra Font.ttf', ['name' => 'Extra Font']],
            ['Extra Font Bold.ttf', ['name' => 'Extra Font', 'bold' => true]],
            ['Extra Font Italic.ttf', ['name' => 'Extra Font', 'italic' => true]],
            ['Extra Font Bold Italic.ttf', ['name' => 'Extra Font', 'bold' => true, 'italic' => true]],
        ];
    }

    /**
     * @dataProvider providerOverride
     */
    public function testOverrideFilenames(string $expected, array $fontArray): void
    {
        Font::setTrueTypeFontPath(self::DEFAULT_DIRECTORY);
        Font::setExtraFontArray([
            'Arial' => [
                'x' => 'extrafont.ttf',
                'xb' => 'extrafontbd.ttf',
                'xi' => 'extrafonti.ttf',
                'xbi' => 'extrafontbi.ttf',
            ],
        ]);
        $font = (new StyleFont())->applyFromArray($fontArray);
        $result = Font::getTrueTypeFontFileFromFont($font);
        self::assertSame($expected, basename($result));
    }

    public static function providerOverride(): array
    {
        return [
            ['extrafont.ttf', ['name' => 'Arial']],
            ['extrafontbd.ttf', ['name' => 'Arial', 'bold' => true]],
            ['extrafonti.ttf', ['name' => 'Arial', 'italic' => true]],
            ['extrafontbi.ttf', ['name' => 'Arial', 'bold' => true, 'italic' => true]],
            ['cour.ttf', ['name' => 'Courier New']],
        ];
    }

    /**
     * @dataProvider providerOverrideAbsolute
     */
    public function testOverrideFilenamesAbsolute(string $expected, array $fontArray): void
    {
        $realPath = realpath(self::MAC_DIRECTORY) . DIRECTORY_SEPARATOR;
        Font::setTrueTypeFontPath(self::DEFAULT_DIRECTORY);
        Font::setExtraFontArray([
            'Arial' => [
                'x' => $realPath . 'Arial.ttf',
                'xb' => $realPath . 'Arial Bold.ttf',
                'xi' => $realPath . 'Arial Italic.ttf',
                'xbi' => $realPath . 'Arial Bold Italic.ttf',
            ],
        ]);
        $font = (new StyleFont())->applyFromArray($fontArray);
        $result = Font::getTrueTypeFontFileFromFont($font);
        self::assertSame($expected, basename($result));
    }

    public static function providerOverrideAbsolute(): array
    {
        return [
            'absolute path normal' => ['Arial.ttf', ['name' => 'Arial']],
            'absolute path bold' => ['Arial Bold.ttf', ['name' => 'Arial', 'bold' => true]],
            'absolute path italic' => ['Arial Italic.ttf', ['name' => 'Arial', 'italic' => true]],
            'absolute path bold italic' => ['Arial Bold Italic.ttf', ['name' => 'Arial', 'bold' => true, 'italic' => true]],
            'non-absolute path uses TrueTypeFontPath' => ['cour.ttf', ['name' => 'Courier New']],
        ];
    }

    /**
     * @dataProvider providerRecurse
     */
    public function testRecurseFilenames(string $expected, array $fontArray): void
    {
        if ($expected === 'exception') {
            $this->expectException(SSException::class);
            $this->expectExceptionMessage('TrueType Font file not found');
        }
        Font::setTrueTypeFontPath(self::RECURSE_DIRECTORY);
        $font = (new StyleFont())->applyFromArray($fontArray);
        $result = Font::getTrueTypeFontFileFromFont($font);
        self::assertSame($expected, basename($result));
    }

    public static function providerRecurse(): array
    {
        return [
            'in subdirectory' => ['arial.ttf', ['name' => 'Arial']],
            'in subdirectory bold' => ['arialbd.ttf', ['name' => 'Arial', 'bold' => true]],
            'in main directory' => ['cour.ttf', ['name' => 'Courier New']],
            'not in main or subdirectory' => ['exception', ['name' => 'Courier New', 'bold' => true]],
        ];
    }
}

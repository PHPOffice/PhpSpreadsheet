<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PaletteColorIndexTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private Workbook $workbook;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function setUpWorkbook(): void
    {
        $this->spreadsheet = $spreadsheet = new Spreadsheet();
        $strTotal = 0;
        $strUnique = 0;
        $strTable = [];
        $colors = [];
        $parser = new Parser($spreadsheet);

        $this->workbook = new Workbook($spreadsheet, $strTotal, $strUnique, $strTable, $colors, $parser);
    }

    /**
     * @return array<int, array{int, int, int, int}>
     */
    private function palette(): array
    {
        /** @var array<int, array{int, int, int, int}> $palette */
        $palette = (new ReflectionClass(Workbook::class))->getProperty('palette')->getValue($this->workbook);

        return $palette;
    }

    /**
     * @return array<string, int>
     */
    private function colorCache(): array
    {
        /** @var array<string, int> $colors */
        $colors = (new ReflectionClass(Workbook::class))->getProperty('colors')->getValue($this->workbook);

        return $colors;
    }

    /**
     * Excel/Biff colors are case-insensitive, so variants must share one cache entry
     * (and therefore one palette slot) instead of being cached separately.
     */
    public function testSameColorInDifferentCaseSharesOneCacheEntry(): void
    {
        $this->setUpWorkbook();

        self::assertSame(9, $this->workbook->addColor('FFFFFF'));
        self::assertSame(9, $this->workbook->addColor('ffffff'));
        self::assertSame(9, $this->workbook->addColor('FfFfFf'));

        self::assertSame(['FFFFFF' => 9], $this->colorCache());
    }

    /**
     * A color that matches an existing (lower) palette entry is cached with that entry's
     * index. If it is inserted last, choosing the next custom index with end() instead of
     * max() makes the next custom color reuse and overwrite the slot of an earlier color.
     */
    public function testCustomColorDoesNotReuseAnOccupiedPaletteSlot(): void
    {
        $this->setUpWorkbook();

        // Built-in colors are cached with the palette index that was found for them.
        $whiteIndex = $this->workbook->addColor('FFFFFF');
        $greenIndex = $this->workbook->addColor('00FF00');

        // First custom color: follows the highest index in use (green's slot).
        $grayIndex = $this->workbook->addColor('636363');

        // Red matches palette index 10, which is lower than the custom slot above.
        $redIndex = $this->workbook->addColor('FF0000');

        // The next custom color must not reuse the gray slot.
        $blueIndex = $this->workbook->addColor('0097FF');

        self::assertSame(9, $whiteIndex);
        self::assertSame(11, $greenIndex);
        self::assertSame(12, $grayIndex);
        self::assertSame(10, $redIndex);
        self::assertSame(13, $blueIndex);

        $palette = $this->palette();
        self::assertSame([0xFF, 0xFF, 0xFF, 0x00], $palette[$whiteIndex]);
        self::assertSame([0x00, 0xFF, 0x00, 0x00], $palette[$greenIndex]);
        self::assertSame([0x63, 0x63, 0x63, 0x00], $palette[$grayIndex]);
        self::assertSame([0xFF, 0x00, 0x00, 0x00], $palette[$redIndex]);
        self::assertSame([0x00, 0x97, 0xFF, 0x00], $palette[$blueIndex]);
    }
}

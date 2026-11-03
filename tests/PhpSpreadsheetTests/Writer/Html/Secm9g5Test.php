<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Secm9g5Test extends TestCase
{
    public static function testCrazyFontName(): void
    {
        $infile = 'tests/data/Reader/HTML/secm9g5.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($infile);
        $writer = new HtmlWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
        self::assertStringContainsString("font-family:'safe\\27 \\3b color\\3a rgb\\28 214\\2c 0\\2c 0\\29 \\3b background-image\\3a url\\28 http\\3a \\2f \\2f 127.0.0.1\\3a 8765\\2f probe.svg\\29 \\3b \\2f \\2a '", $html);

        $reader = new HtmlReader();
        $spreadsheet2 = $reader->loadFromString($html);
        $sheet = $spreadsheet2->getActiveSheet();
        $font = $sheet->getStyle('A1')->getFont();
        self::assertSame(
            "safe';color:rgb(214,0,0);background-image:url(http://127.0.0.1:8765/probe.svg);/*",
            $font->getName()
        );
        self::assertEqualsWithDelta(12.0, $font->getSize(), 1.0e-8, 'make sure font size is not treated as within css comment');
        self::assertSame('000000', $font->getColor()->getRgb(), 'make sure color specification in font-family is ignored for font color');
        $font = $sheet->getStyle('A2')->getFont();
        self::assertSame('Arial', $font->getName());
        self::assertEqualsWithDelta(10.0, $font->getSize(), 1.0e-8);
        self::assertSame('000000', $font->getColor()->getRgb());
        $spreadsheet2->disconnectWorksheets();
    }

    #[DataProvider('providerNeedEscaping')]
    public function testNeedEscaping(string $expected, string $input): void
    {
        self::assertSame($expected, HtmlWriter::cssEscape($input));
    }

    /** @return array<array{string, string}> */
    public static function providerNeedEscaping(): array
    {
        return [
            'no special chars' => ['Times New Roman', 'Times New Roman'],
            'non-latin word chars' => ['αΒϓΔ', 'αΒϓΔ'],
            'non-word chars' => ['\2190 \2191 \2192 \2193 ', '←↑→↓'],
            'problematic chars' => ['\5c \26 \3c \3e \27 \22 ', '\&<>\'"'],
            'newline' => ['hello\a goodbye', 'hello
goodbye'],
        ];
    }
}

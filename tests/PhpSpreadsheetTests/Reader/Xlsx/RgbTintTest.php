<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class RgbTintTest extends TestCase
{
    public static function compareColors(string $style, string $text): string
    {
        $styleRed = hexdec(substr($style, 0, 2));
        $styleGreen = hexdec(substr($style, 2, 2));
        $styleBlue = hexdec(substr($style, 4, 2));
        $textRed = hexdec(substr($text, 0, 2));
        $textGreen = hexdec(substr($text, 2, 2));
        $textBlue = hexdec(substr($text, 4, 2));
        $maxDiff = 3;
        if (abs($styleRed - $textRed) > $maxDiff) {
            return $style;
        }
        if (abs($styleGreen - $textGreen) > $maxDiff) {
            return $style;
        }
        if (abs($styleBlue - $textBlue) > $maxDiff) {
            return $style;
        }

        return $text;
    }

    public function testRgbTint(): void
    {
        $filename = 'tests/data/Reader/XLSX/RgbTint.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $row = 0;
        while (true) {
            ++$row;
            $text = (string) $sheet->getCell("B$row");
            if ($text === '') {
                break;
            }
            $style = $sheet->getStyle("A$row")->getFill()->getStartColor()->getRgb();
            self::assertSame($text, self::compareColors($style, $text), "row $row");
        }
        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PHPUnit\Framework\TestCase;

class FreezeTest extends TestCase
{
    public static function testFreeze(): void
    {
        // We can write FreezePane data to Ods but we cannot yet read it.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['A', 'B', 'C', 'D'],
            [1, 2, 3, 4],
        ]);
        $sheet->freezePane('B2');
        $writer = new OdsWriter($spreadsheet);
        $writerSettings = new OdsWriter\Settings($writer);
        $settings = $writerSettings->write();
        // The items we are particular interested in below are:
        // HorizontalSplitMode, HorizontalSplitPosition, PositionLeft, PositionRight
        // VerticalSplitMode, VerticalSplitPosition, PositionTop, PositionBottom
        $expected = '<config:config-item-map-entry config:name="Worksheet">'
            . '<config:config-item config:name="CursorPositionX" config:type="int">0</config:config-item>'
            . '<config:config-item config:name="CursorPositionY" config:type="int">0</config:config-item>'
            . '<config:config-item config:name="HorizontalSplitMode" config:type="short">2</config:config-item>'
            . '<config:config-item config:name="HorizontalSplitPosition" config:type="int">1</config:config-item>'
            . '<config:config-item config:name="PositionLeft" config:type="short">0</config:config-item>'
            . '<config:config-item config:name="PositionRight" config:type="short">1</config:config-item>'
            . '<config:config-item config:name="VerticalSplitMode" config:type="short">2</config:config-item>'
            . '<config:config-item config:name="VerticalSplitPosition" config:type="int">1</config:config-item>'
            . '<config:config-item config:name="PositionTop" config:type="short">0</config:config-item>'
            . '<config:config-item config:name="PositionBottom" config:type="short">1</config:config-item>'
            . '<config:config-item config:name="ActiveSplitRange" config:type="short">3</config:config-item>'
            . '</config:config-item-map-entry>';
        self::assertStringContainsString($expected, $settings);
        $spreadsheet->disconnectWorksheets();
    }
}

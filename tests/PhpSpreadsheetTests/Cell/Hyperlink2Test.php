<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Hyperlink2Test extends TestCase
{
    public function testTwoLiterals(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.net';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', "=HYPERLINK(\"$actualUrl\", \"$toolTip\")");
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());
        // No hyperlink should be created for A1 or B1 - issue 3889
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $hyperlink = $sheet->getCell('B1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellAndLiteral(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.org';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', "=HYPERLINK(A1, \"$toolTip\")");
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());
        // No hyperlink should be created for A1 or B1 - issue 3889
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $hyperlink = $sheet->getCell('B1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $spreadsheet->disconnectWorksheets();
    }

    public function testLiteralAndCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.edu';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', "=HYPERLINK(\"$actualUrl\", B1)");
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());
        // No hyperlink should be created for A1 or B1 - issue 3889
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $hyperlink = $sheet->getCell('B1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $spreadsheet->disconnectWorksheets();
    }

    public static function testTwoCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.com';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', '=HYPERLINK(A1, B1)');
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());
        // No hyperlink should be created for A1 or B1 - issue 3889
        $hyperlink = $sheet->getCell('A1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $hyperlink = $sheet->getCell('B1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
        $spreadsheet->disconnectWorksheets();
    }

    public function testResetOnSet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.com';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', '=HYPERLINK(A1, B1)');
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());

        $sheet->setCellValue('C1', 123);
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
    }

    public function testResetOnSetExplicit(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $basicUrl = 'example.com';
        $actualUrl = "https://www.$basicUrl";
        $toolTip = "Link to $basicUrl";
        $sheet->setCellValue('A1', $actualUrl);
        $sheet->setCellValue('B1', $toolTip);
        $sheet->setCellValue('C1', '=HYPERLINK(A1, B1)');
        $result = $sheet->getCell('C1')->getCalculatedValue();
        self::assertSame($toolTip, $sheet->getCell('C1')->getCalculatedValue());
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame($actualUrl, $hyperlink->getUrl());
        self::assertSame($toolTip, $hyperlink->getTooltip());

        $sheet->setCellValueExplicit('C1', '123', DataType::TYPE_STRING);
        $hyperlink = $sheet->getCell('C1')->getHyperlink();
        self::assertSame('', $hyperlink->getUrl());
        self::assertSame('', $hyperlink->getTooltip());
    }
}

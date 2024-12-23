<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use PHPUnit\Framework\TestCase;

class IndentTest extends TestCase
{
    private string $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(
            Functions::COMPATIBILITY_OPENOFFICE
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    public function testWriteSpreadsheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'aa');
        $sheet->setCellValue('B1', 'bb');
        $sheet->setCellValue('A2', 'cc');
        $sheet->setCellValue('B2', 'dd');
        $sheet->getStyle('A1')->getAlignment()->setIndent(2);
        $writer = new Ods($spreadsheet);
        $content = new Content($writer);
        $xml = $content->write();
        self::assertStringContainsString(
            '<style:style style:name="ce0" style:family="table-cell" style:parent-style-name="Default">'
                . '<style:table-cell-properties style:vertical-align="bottom" style:rotation-align="none"/>'
                . '<style:text-properties fo:color="#000000" fo:font-family="Calibri" fo:font-size="11.0pt"/>'
                . '</style:style>',
            $xml
        );
        self::assertStringContainsString(
            '<style:style style:name="ce1" style:family="table-cell" style:parent-style-name="Default">'
                . '<style:table-cell-properties style:vertical-align="bottom" style:rotation-align="none"/>'
                . '<style:paragraph-properties fo:margin-left="0.2086in"/>' // fo:margin-left is what we're looking for
                . '<style:text-properties fo:color="#000000" fo:font-family="Calibri" fo:font-size="11.0pt"/>'
                . '</style:style>',
            $xml
        );
        self::assertSame(3, substr_count($xml, 'table:style-name="ce0"'));
        self::assertSame(1, substr_count($xml, 'table:style-name="ce1"'));
        $spreadsheet->disconnectWorksheets();
    }
}

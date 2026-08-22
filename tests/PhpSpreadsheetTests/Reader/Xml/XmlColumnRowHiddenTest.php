<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlColumnRowHiddenTest extends TestCase
{
    public function testColumnRowHidden(): void
    {
        $xmldata = <<< 'EOT'
            <?xml version="1.0" encoding="UTF-8"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
                <Worksheet ss:Name="1">
                    <ss:Table>
                        <Column ss:Hidden="1" />
                        <Column ss:Hidden="0" />
                        <Column />
                        <ss:Row ss:Hidden="1">
                            <ss:Cell>
                                <ss:Data ss:Type="String">hidden row and hidden column</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">hidden row and visible column</ss:Data>
                            </ss:Cell>
                        </ss:Row>
                        <ss:Row ss:Hidden="0">
                            <ss:Cell>
                                <ss:Data ss:Type="String">visible row and hidden column</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">visible row and visible column</ss:Data>
                            </ss:Cell>
                        </ss:Row>
                        <ss:Row />
                    </ss:Table>
                </Worksheet>
            </Workbook>
            EOT;
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString($xmldata);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('1', $sheet->getTitle());
        self::assertFalse($sheet->getColumnDimension('A')->getVisible());
        self::assertTrue($sheet->getColumnDimension('B')->getVisible());
        self::assertTrue($sheet->getColumnDimension('C')->getVisible());
        self::assertFalse($sheet->getRowDimension(1)->getVisible());
        self::assertTrue($sheet->getRowDimension(2)->getVisible());
        self::assertTrue($sheet->getRowDimension(3)->getVisible());

        $spreadsheet->disconnectWorksheets();
    }
}

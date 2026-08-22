<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlProtectionTest extends TestCase
{
    public function testProtection(): void
    {
        $xmldata = <<< 'EOT'
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
                <Styles>
                    <Style ss:ID="Default" ss:Name="Normal">
                        <Protection x:HideFormula="1" ss:Protected="1"/>
                    </Style>
                    <Style ss:ID="visible">
                        <Protection x:HideFormula="0" ss:Protected="1"/>
                    </Style>
                    <Style ss:ID="writable">
                        <Protection x:HideFormula="1" ss:Protected="0"/>
                    </Style>
                    <Style ss:ID="neither">
                    </Style>
                </Styles>
                <Worksheet ss:Name="sheet 1" ss:Protected="1">
                    <ss:Table>
                        <ss:Row>
                            <ss:Cell ss:Formula="=1">
                                <ss:Data ss:Type="Number"/>
                            </ss:Cell>
                            <ss:Cell ss:StyleID="visible" ss:Formula="=2">
                                <ss:Data ss:Type="Number"/>
                            </ss:Cell>
                            <ss:Cell ss:StyleID="writable" ss:Formula="=3">
                                <ss:Data ss:Type="Number"/>
                            </ss:Cell>
                        </ss:Row>
                    </ss:Table>
                </Worksheet>
            </Workbook>
            EOT;
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString($xmldata);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('sheet 1', $sheet->getTitle());
        self::assertTrue($sheet->getProtection()->isProtectionEnabled());
        self::assertSame('protected', $sheet->getCell('A1')->getStyle()->getProtection()->getLocked());
        self::assertSame('protected', $sheet->getCell('A1')->getStyle()->getProtection()->getHidden());
        self::assertSame('protected', $sheet->getCell('B1')->getStyle()->getProtection()->getLocked());
        self::assertSame('unprotected', $sheet->getCell('B1')->getStyle()->getProtection()->getHidden());
        self::assertSame('unprotected', $sheet->getCell('C1')->getStyle()->getProtection()->getLocked());
        self::assertSame('protected', $sheet->getCell('C1')->getStyle()->getProtection()->getHidden());

        $spreadsheet->disconnectWorksheets();
    }
}

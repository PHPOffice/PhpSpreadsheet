<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlTopLeftTest extends TestCase
{
    public function testTopLeft(): void
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
                </Styles>
                <Worksheet ss:Name="sheet 1" ss:Protected="1">
                    <ss:Table>
                        <ss:Row>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.1</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.2</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.3</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.4</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.5</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">1.6</ss:Data>
                            </ss:Cell>
                        </ss:Row>
                        <ss:Row>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.1</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.2 (top-left cell)</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.3</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.4</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.5</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">2.6</ss:Data>
                            </ss:Cell>
                        </ss:Row>
                        <ss:Row>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.1</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.2</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.3</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.4</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.5</ss:Data>
                            </ss:Cell>
                            <ss:Cell>
                                <ss:Data ss:Type="String">3.6</ss:Data>
                            </ss:Cell>
                        </ss:Row>
                    </ss:Table>
                    <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
                        <TopRowVisible>1</TopRowVisible>
                        <LeftColumnVisible>1</LeftColumnVisible>
                    </WorksheetOptions>
                </Worksheet>
            </Workbook>
            EOT;
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString($xmldata);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('sheet 1', $sheet->getTitle());
        self::assertEquals('B2', $sheet->getTopLeftCell());

        $spreadsheet->disconnectWorksheets();
    }
}

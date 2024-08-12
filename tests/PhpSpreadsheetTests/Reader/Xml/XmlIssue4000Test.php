<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PHPUnit\Framework\TestCase;

class XmlIssue4000Test extends TestCase
{
    public function testFontBoldItalic(): void
    {
        // Styles tag and children have ss: prefixes
        $xmldata = <<< 'EOT'
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
             xmlns:o="urn:schemas-microsoft-com:office:office"
             xmlns:x="urn:schemas-microsoft-com:office:excel"
             xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
             xmlns:html="http://www.w3.org/TR/REC-html40">
             <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
              <Version>16.00</Version>
             </DocumentProperties>
             <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
              <AllowPNG/>
             </OfficeDocumentSettings>
             <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
              <WindowHeight>6510</WindowHeight>
              <WindowWidth>19200</WindowWidth>
              <WindowTopX>32767</WindowTopX>
              <WindowTopY>32767</WindowTopY>
              <ProtectStructure>False</ProtectStructure>
              <ProtectWindows>False</ProtectWindows>
             </ExcelWorkbook>
             <ss:Styles>
              <ss:Style ss:ID="Default" ss:Name="Normal">
               <ss:Alignment ss:Vertical="Bottom"/>
               <ss:Borders/>
               <ss:Font ss:FontName="Aptos Narrow" x:Family="Swiss" ss:Size="11"
                ss:Color="#000000"/>
               <ss:Interior/>
               <ss:NumberFormat/>
               <ss:Protection/>
              </ss:Style>
              <ss:Style ss:ID="s63">
               <ss:Alignment ss:Vertical="Bottom"/>
               <ss:Borders>
                <ss:Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3"/>
                <ss:Border ss:Position="Left" ss:LineStyle="Continuous"/>
                <ss:Border ss:Position="Right" ss:LineStyle="DashDot" ss:Weight="2"/>
                <ss:Border ss:Position="Top" ss:LineStyle="Dash" ss:Weight="1"/>
               </ss:Borders>
               <ss:Font ss:FontName="Aptos Narrow" x:Family="Swiss" ss:Size="11"
                ss:Color="#000000" ss:Bold="1"/>
               <ss:Interior/>
               <ss:NumberFormat/>
               <ss:Protection/>
              </ss:Style>
             </ss:Styles>
             <Worksheet ss:Name="Test">
              <Table ss:ExpandedColumnCount="2" ss:ExpandedRowCount="2" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row ss:Index="2" ss:Height="15">
                <Cell ss:Index="2" ss:StyleID="s63"><Data ss:Type="String">TEST</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <Selected/>
               <ProtectObjects>False</ProtectObjects>
               <ProtectScenarios>False</ProtectScenarios>
              </WorksheetOptions>
             </Worksheet>
            </Workbook>
            EOT;
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString($xmldata);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Test', $sheet->getTitle());
        self::assertSame(Border::BORDER_DOUBLE, $sheet->getStyle('B2')->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_MEDIUMDASHDOT, $sheet->getStyle('B2')->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $sheet->getStyle('B2')->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_HAIR, $sheet->getStyle('B2')->getBorders()->getLeft()->getBorderStyle());

        $spreadsheet->disconnectWorksheets();
    }
}

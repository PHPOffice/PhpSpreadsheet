<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlColSpanTest extends TestCase
{
    public function testColSpan(): void
    {
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
              <WindowHeight>6820</WindowHeight>
              <WindowWidth>19200</WindowWidth>
              <WindowTopX>32767</WindowTopX>
              <WindowTopY>32767</WindowTopY>
              <ProtectStructure>False</ProtectStructure>
              <ProtectWindows>False</ProtectWindows>
             </ExcelWorkbook>
             <Styles>
              <Style ss:ID="Default" ss:Name="Normal">
               <Alignment ss:Vertical="Bottom"/>
               <Borders/>
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
               <Interior/>
               <NumberFormat/>
               <Protection/>
              </Style>
             </Styles>
             <Worksheet ss:Name="Tabelle1">
              <Table ss:ExpandedColumnCount="6" ss:ExpandedRowCount="1" x:FullColumns="1"
               x:FullRows="1" ss:DefaultColumnWidth="60" ss:DefaultRowHeight="14.5">
               <Column ss:AutoFitWidth="0" ss:Width="76.5"/>
               <Column ss:AutoFitWidth="0" ss:Width="25.5" ss:Span="3"/>
               <Row ss:AutoFitHeight="0">
                <Cell><Data ss:Type="String">wide</Data></Cell>
                <Cell><Data ss:Type="String">thin</Data></Cell>
                <Cell><Data ss:Type="String">thin</Data></Cell>
                <Cell><Data ss:Type="String">thin</Data></Cell>
                <Cell><Data ss:Type="String">thin</Data></Cell>
                <Cell><Data ss:Type="String">normal</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <Unsynced/>
               <Selected/>
               <Panes>
                <Pane>
                 <Number>3</Number>
                 <ActiveRow>1</ActiveRow>
                </Pane>
               </Panes>
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
        self::assertSame('Tabelle1', $sheet->getTitle());
        self::assertSame('A2', $sheet->getSelectedCells());
        $widthMultiplier = 5.4;
        $expectedWidthBtoE = 25.5 / $widthMultiplier;
        self::assertEqualsWithDelta($expectedWidthBtoE, $sheet->getColumnDimension('B')->getWidth(), 1E-6, '1st col in span');
        self::assertEqualsWithDelta($expectedWidthBtoE, $sheet->getColumnDimension('C')->getWidth(), 1E-6, '2nd col in span');
        self::assertEqualsWithDelta($expectedWidthBtoE, $sheet->getColumnDimension('D')->getWidth(), 1E-6, '3rd col in span');
        self::assertEqualsWithDelta($expectedWidthBtoE, $sheet->getColumnDimension('E')->getWidth(), 1E-6, '4th col in span');
        $expectedWidthA = 76.5 / $widthMultiplier;
        self::assertEqualsWithDelta($expectedWidthA, $sheet->getColumnDimension('A')->getWidth(), 1E-6, 'width without span');
        self::assertEqualsWithDelta(-1.0, $sheet->getColumnDimension('F')->getWidth(), 1E-6, 'default width');

        $spreadsheet->disconnectWorksheets();
    }
}

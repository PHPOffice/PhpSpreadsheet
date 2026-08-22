<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlActiveSheetTest extends TestCase
{
    public function testActiveSheet(): void
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
              <ActiveSheet>1</ActiveSheet>
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
             <Worksheet ss:Name="sheet 1">
              <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row>
                <Cell><Data ss:Type="String">Sheet 1</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <ProtectObjects>False</ProtectObjects>
               <ProtectScenarios>False</ProtectScenarios>
              </WorksheetOptions>
             </Worksheet>
             <Worksheet ss:Name="sheet 2">
              <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row>
                <Cell><Data ss:Type="String">Sheet 2</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <Selected/>
               <Panes>
                 <Pane>
                   <Number>3</Number>
                   <ActiveRow>2</ActiveRow>
                   <ActiveCol>2</ActiveCol>
                 </Pane>
               </Panes>
               <ProtectObjects>False</ProtectObjects>
               <ProtectScenarios>False</ProtectScenarios>
              </WorksheetOptions>
             </Worksheet>
             <Worksheet ss:Name="sheet 3">
              <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row>
                <Cell><Data ss:Type="String">Sheet 3</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <PageSetup>
                <Header x:Margin="0.3"/>
                <Footer x:Margin="0.3"/>
                <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
               </PageSetup>
               <Panes>
                 <Pane>
                   <Number>3</Number>
                   <ActiveRow>3</ActiveRow>
                   <ActiveCol>2</ActiveCol>
                   <RangeSelection>R4C3:R4C4</RangeSelection>
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
        self::assertEquals(3, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('sheet 2', $sheet->getTitle());
        self::assertSame('C3', $sheet->getSelectedCells());
        $sheet = $spreadsheet->getSheetByNameOrThrow('sheet 3');
        self::assertSame('C4:D4', $sheet->getSelectedCells());

        $spreadsheet->disconnectWorksheets();
    }
}

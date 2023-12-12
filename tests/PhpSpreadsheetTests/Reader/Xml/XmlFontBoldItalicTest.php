<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlFontBoldItalicTest extends TestCase
{
    public function testFontBoldItalic(): void
    {
        $xmldata = <<< 'EOT'
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
             xmlns:o="urn:schemas-microsoft-com:office:office"
             xmlns:x="urn:schemas-microsoft-com:office:excel"
             xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
             xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
             xmlns:html="http://www.w3.org/TR/REC-html40">
             <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
              <Author>Owen Leibman</Author>
              <LastAuthor>Owen Leibman</LastAuthor>
              <Created>2023-05-15T17:04:33Z</Created>
              <Version>16.00</Version>
             </DocumentProperties>
             <CustomDocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Enabled dt:dt="string">true</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Enabled>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_SetDate dt:dt="string">2023-05-15T17:06:02Z</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_SetDate>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Method dt:dt="string">Standard</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Method>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Name dt:dt="string">defa4170-0d19-0005-0004-bc88714345d2</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_Name>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_SiteId dt:dt="string">f9465cb1-7889-4d9a-b552-fdd0addf0eb1</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_SiteId>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_ActionId dt:dt="string">55f09022-fb59-44b6-a7c5-9d50704243d3</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_ActionId>
              <MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_ContentBits dt:dt="string">0</MSIP_Label_b002e552-5bac-4e77-a8f2-1e8fdcadcedf_ContentBits>
             </CustomDocumentProperties>
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
              <Style ss:ID="s62">
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
                ss:Bold="1"/>
              </Style>
              <Style ss:ID="s62nb">
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
                ss:Bold="0"/>
              </Style>
              <Style ss:ID="s64">
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
                ss:Bold="1" ss:Italic="1"/>
              </Style>
              <Style ss:ID="s65">
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
                ss:Italic="1"/>
              </Style>
              <Style ss:ID="s65ni">
               <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
                ss:Italic="0"/>
              </Style>
             </Styles>
             <Worksheet ss:Name="Sheet1">
              <Table ss:ExpandedColumnCount="2" ss:ExpandedRowCount="4" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row ss:AutoFitHeight="0">
                <Cell><Data ss:Type="String">normal</Data></Cell>
                <Cell><Data ss:Type="String">normal</Data></Cell>
               </Row>
               <Row ss:AutoFitHeight="0">
                <Cell ss:StyleID="s62"><Data ss:Type="String">bold</Data></Cell>
                <Cell ss:StyleID="s62nb"><Data ss:Type="String">not bold</Data></Cell>
               </Row>
               <Row ss:AutoFitHeight="0">
                <Cell ss:StyleID="s65"><Data ss:Type="String">italic</Data></Cell>
                <Cell ss:StyleID="s65ni"><Data ss:Type="String">not italic</Data></Cell>
               </Row>
               <Row ss:AutoFitHeight="0">
                <Cell ss:StyleID="s64"><Data ss:Type="String">bold italic</Data></Cell>
                <Cell ss:StyleID="s64"><Data ss:Type="String">bold italic</Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <PageSetup>
                <Header x:Margin="0.3"/>
                <Footer x:Margin="0.3"/>
                <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
               </PageSetup>
               <Unsynced/>
               <Print>
                <ValidPrinterInfo/>
                <HorizontalResolution>600</HorizontalResolution>
                <VerticalResolution>600</VerticalResolution>
               </Print>
               <Selected/>
               <Panes>
                <Pane>
                 <Number>3</Number>
                 <ActiveRow>2</ActiveRow>
                 <ActiveCol>1</ActiveCol>
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
        self::assertEquals('Sheet1', $sheet->getTitle());
        self::assertFalse($sheet->getStyle('A1')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('A1')->getFont()->getItalic());
        self::assertTrue($sheet->getStyle('A2')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('A2')->getFont()->getItalic());
        self::assertFalse($sheet->getStyle('A3')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('A3')->getFont()->getItalic());
        self::assertTrue($sheet->getStyle('A4')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('A4')->getFont()->getItalic());

        self::assertFalse($sheet->getStyle('B1')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('B1')->getFont()->getItalic());
        self::assertFalse($sheet->getStyle('B2')->getFont()->getBold(), 'xml specifies bold=0');
        self::assertFalse($sheet->getStyle('B2')->getFont()->getItalic());
        self::assertFalse($sheet->getStyle('B3')->getFont()->getBold());
        self::assertFalse($sheet->getStyle('B3')->getFont()->getItalic(), 'xml specifies italic=0');
        self::assertTrue($sheet->getStyle('B4')->getFont()->getBold());
        self::assertTrue($sheet->getStyle('B4')->getFont()->getItalic());

        $spreadsheet->disconnectWorksheets();
    }
}

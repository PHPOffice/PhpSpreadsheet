<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class XmlIssue4002Test extends AbstractFunctional
{
    private const XML_DATA = <<< 'EOT'
        <?xml version="1.0"?>
        <?mso-application progid="Excel.Sheet"?>
        <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
         xmlns:o="urn:schemas-microsoft-com:office:office"
         xmlns:x="urn:schemas-microsoft-com:office:excel"
         xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
         xmlns:html="http://www.w3.org/TR/REC-html40">
         <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
          <LastAuthor>Owen Leibman</LastAuthor>
          <Created>2024-04-25T19:31:48Z</Created>
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
         <Styles>
          <Style ss:ID="Default" ss:Name="Normal">
           <Alignment ss:Vertical="Bottom"/>
           <Borders/>
           <Font ss:FontName="Aptos Narrow" x:Family="Swiss" ss:Size="11"
            ss:Color="#000000"/>
           <Interior/>
           <NumberFormat/>
           <Protection/>
          </Style>
          <Style ss:ID="s62">
           <Alignment ss:Vertical="Bottom"/>
           <Borders>
            <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3"/>
            <Border ss:Position="Left" ss:LineStyle="Continuous"/>
            <Border ss:Position="Right" ss:LineStyle="DashDot" ss:Weight="2"/>
            <Border ss:Position="Top" ss:LineStyle="Dash" ss:Weight="1"/>
           </Borders>
           <Font ss:FontName="Aptos Narrow" x:Family="Swiss" ss:Size="11"
            ss:Color="#000000" ss:Bold="1"/>
           <Interior/>
           <NumberFormat/>
           <Protection/>
          </Style>
         </Styles>
         <Worksheet ss:Name="Test">
          <Table ss:ExpandedColumnCount="5" ss:ExpandedRowCount="4" x:FullColumns="1"
           x:FullRows="1" ss:DefaultRowHeight="14.5">
           <Row ss:AutoFitHeight="0"/>
           <Row ss:AutoFitHeight="0" ss:Height="15">
            <Cell ss:Index="2" ss:StyleID="s62"><Data ss:Type="String">TEST</Data></Cell>
            <Cell ss:Index="5"><Data ss:Type="String">Vertical</Data></Cell>
           </Row>
           <Row ss:AutoFitHeight="0"/>
           <Row ss:AutoFitHeight="0">
            <Cell ss:Index="2"><Data ss:Type="String">New Page</Data></Cell>
            <Cell ss:Index="5"><Data ss:Type="String">Last</Data></Cell>
           </Row>
          </Table>
          <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
           <Unsynced/>
           <Print>
            <ValidPrinterInfo/>
            <HorizontalResolution>600</HorizontalResolution>
            <VerticalResolution>600</VerticalResolution>
           </Print>
           <ShowPageBreakZoom/>
           <Zoom>200</Zoom>
           <PageBreakZoom>200</PageBreakZoom>
           <Selected/>
           <Panes>
            <Pane>
             <Number>3</Number>
             <ActiveRow>2</ActiveRow>
             <ActiveCol>3</ActiveCol>
            </Pane>
           </Panes>
           <ProtectObjects>False</ProtectObjects>
           <ProtectScenarios>False</ProtectScenarios>
          </WorksheetOptions>
          <PageBreaks xmlns="urn:schemas-microsoft-com:office:excel">
           <ColBreaks>
            <ColBreak>
             <Column>3</Column>
            </ColBreak>
           </ColBreaks>
           <RowBreaks>
            <RowBreak>
             <Row>2</Row>
            </RowBreak>
           </RowBreaks>
          </PageBreaks>
         </Worksheet>
        </Workbook>
        EOT;

    public function testZoomAndPageBreaks(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString(self::XML_DATA);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Test', $sheet->getTitle());
        self::assertSame(Border::BORDER_DOUBLE, $sheet->getStyle('B2')->getBorders()->getBottom()->getBorderStyle());
        self::assertSame(Border::BORDER_MEDIUMDASHDOT, $sheet->getStyle('B2')->getBorders()->getRight()->getBorderStyle());
        self::assertSame(Border::BORDER_DASHED, $sheet->getStyle('B2')->getBorders()->getTop()->getBorderStyle());
        self::assertSame(Border::BORDER_HAIR, $sheet->getStyle('B2')->getBorders()->getLeft()->getBorderStyle());

        self::assertSame(['A2'], array_keys($sheet->getRowBreaks()));
        self::assertSame(['D1'], array_keys($sheet->getColumnBreaks()));
        $sheetView = $sheet->getSheetView();
        self::assertSame(200, $sheetView->getZoomScale());
        self::assertSame(200, $sheetView->getZoomScaleNormal());
        self::assertSame(100, $sheetView->getZoomScalePageLayoutView());
        self::assertSame(200, $sheetView->getZoomScaleSheetLayoutView());

        $spreadsheet->disconnectWorksheets();
    }

    public function testXlsxWriterAndReader(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString(self::XML_DATA);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(['A2'], array_keys($rsheet->getRowBreaks()));
        self::assertSame(['D1'], array_keys($rsheet->getColumnBreaks()));
        $rsheetView = $rsheet->getSheetView();
        self::assertSame(200, $rsheetView->getZoomScale());
        self::assertSame(200, $rsheetView->getZoomScaleNormal());
        self::assertSame(100, $rsheetView->getZoomScalePageLayoutView());
        self::assertSame(200, $rsheetView->getZoomScaleSheetLayoutView());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testXlsWriterAndReader(): void
    {
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString(self::XML_DATA);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(['A2'], array_keys($rsheet->getRowBreaks()));
        self::assertSame(['D1'], array_keys($rsheet->getColumnBreaks()));
        $rsheetView = $rsheet->getSheetView();
        self::assertSame(200, $rsheetView->getZoomScale());
        self::assertSame(200, $rsheetView->getZoomScaleNormal());
        self::assertSame(100, $rsheetView->getZoomScalePageLayoutView());
        self::assertSame(100, $rsheetView->getZoomScaleSheetLayoutView(), 'different value than Xml or Xlsx but I do not see any consequence of that');
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}

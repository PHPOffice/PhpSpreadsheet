<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PHPUnit\Framework\TestCase;

class XmlRichTextTest extends TestCase
{
    public function testBreakTag(): void
    {
        $xmldata = <<< 'EOT'
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                xmlns:html="http://www.w3.org/TR/REC-html40">
            <Worksheet ss:Name="Test">
                <ss:Table>
                    <ss:Row>
                        <ss:Cell>
                            <ss:Data ss:Type="String" xmlns="http://www.w3.org/TR/REC-html40"><I>italic</I><B>bold</B><BR />second line</ss:Data>
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
        self::assertEquals('Test', $sheet->getTitle());
        $richText = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $richText);
        $elements = $richText->getRichTextElements();
        self::assertCount(3, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame('italic', $run->getText());
        self::assertNotNull($run->getFont());
        self::assertTrue($run->getFont()->getItalic());
        self::assertFalse($run->getFont()->getBold());

        $run = $elements[1];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame('bold', $run->getText());
        self::assertNotNull($run->getFont());
        self::assertFalse($run->getFont()->getItalic());
        self::assertTrue($run->getFont()->getBold());

        $run = $elements[2];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame("\nsecond line", $run->getText());

        $spreadsheet->disconnectWorksheets();
    }

    public function testNewlineAndFontTag(): void
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
              <LastAuthor>Owen Leibman</LastAuthor>
              <Created>2024-04-28T06:03:14Z</Created>
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
              <Style ss:ID="s63">
               <Alignment ss:Vertical="Bottom" ss:WrapText="1"/>
               <Borders/>
               <Font ss:FontName="Aptos Narrow" x:Family="Swiss" ss:Size="11" ss:Italic="1"/>
               <Interior/>
               <NumberFormat/>
               <Protection/>
              </Style>
             </Styles>
             <Worksheet ss:Name="Test">
              <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
               x:FullRows="1" ss:DefaultRowHeight="14.5">
               <Row ss:AutoFitHeight="0" ss:Height="47.5">
                <Cell ss:StyleID="s63"><ss:Data ss:Type="String"
                  xmlns="http://www.w3.org/TR/REC-html40"><I>italic</I><B>bold&#10;</B><Font
                   html:Color="#FF0000">second</Font><Font> line</Font></ss:Data></Cell>
               </Row>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <Unsynced/>
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
        $richText = $sheet->getCell('A1')->getValue();
        self::assertInstanceOf(RichText::class, $richText);
        $elements = $richText->getRichTextElements();
        self::assertCount(4, $elements);
        $run = $elements[0];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame('italic', $run->getText());
        self::assertNotNull($run->getFont());
        self::assertTrue($run->getFont()->getItalic());
        self::assertFalse($run->getFont()->getBold());

        $run = $elements[1];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame("bold\n", $run->getText());
        self::assertNotNull($run->getFont());
        self::assertFalse($run->getFont()->getItalic());
        self::assertTrue($run->getFont()->getBold());

        $run = $elements[2];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame('second', $run->getText());
        self::assertSame('FF0000', $run->getFont()?->getColor()->getRgb());

        $run = $elements[3];
        self::assertInstanceOf(Run::class, $run);
        self::assertSame(' line', $run->getText());

        $spreadsheet->disconnectWorksheets();
    }
}

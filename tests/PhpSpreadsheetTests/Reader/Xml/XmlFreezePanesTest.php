<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class XmlFreezePanesTest extends TestCase
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
             <Worksheet ss:Name="Tabelle1">
              <Table>
              </Table>
              <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
               <FreezePanes/>
               <SplitHorizontal>2</SplitHorizontal>
               <TopRowBottomPane>10</TopRowBottomPane>
               <SplitVertical>6</SplitVertical>
               <LeftColumnRightPane>20</LeftColumnRightPane>
              </WorksheetOptions>
             </Worksheet>
            </Workbook>
            EOT;
        $reader = new Xml();
        $spreadsheet = $reader->loadSpreadsheetFromString($xmldata);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Tabelle1', $sheet->getTitle());
        self::assertSame('G3', $sheet->getFreezePane());

        $spreadsheet->disconnectWorksheets();
    }
}

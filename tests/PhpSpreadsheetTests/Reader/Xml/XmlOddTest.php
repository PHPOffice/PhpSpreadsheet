<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class XmlOddTest extends TestCase
{
    private string $filename = '';

    protected function teardown(): void
    {
        if ($this->filename) {
            unlink($this->filename);
            $this->filename = '';
        }
    }

    public function testWriteThenRead(): void
    {
        $xmldata = <<< 'EOT'
            <?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
                      xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
                      xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                      >
             <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
              <Title>Xml2003 Short Workbook</Title>
             </DocumentProperties>
             <CustomDocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
              <my_x05d0_Int dt:dt="integer">2</my_x05d0_Int>
             </CustomDocumentProperties>
                <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
                    <WindowHeight>9000</WindowHeight>
                    <WindowWidth>13860</WindowWidth>
                    <WindowTopX>240</WindowTopX>
                    <WindowTopY>75</WindowTopY>
                    <ProtectStructure>False</ProtectStructure>
                    <ProtectWindows>False</ProtectWindows>
                </ExcelWorkbook>
                <ss:Worksheet ss:Name="Sample Data">
                    <Table>
                        <Column ss:Width="96.4913"/>
                        <Row ss:Index="8" ss:AutoFitHeight="0" ss:Height="14.9953">
                            <Cell>
                                <ss:Data ss:Type="String">Test String 1</ss:Data>
                            </Cell>
                        </Row>
                    </Table>
                </ss:Worksheet>
            </Workbook>
            EOT;
        $this->filename = File::temporaryFilename();
        file_put_contents($this->filename, $xmldata);
        $reader = new Xml();
        $spreadsheet = $reader->load($this->filename);
        self::assertEquals(1, $spreadsheet->getSheetCount());

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Sample Data', $sheet->getTitle());
        self::assertEquals('Test String 1', $sheet->getCell('A8')->getValue());

        $props = $spreadsheet->getProperties();
        self::assertEquals('Xml2003 Short Workbook', $props->getTitle());
        self::assertEquals('2', $props->getCustomPropertyValue('myאInt'));
        $spreadsheet->disconnectWorksheets();
    }
}

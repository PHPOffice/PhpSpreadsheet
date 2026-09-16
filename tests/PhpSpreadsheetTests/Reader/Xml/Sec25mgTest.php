<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class Sec25mgTest extends TestCase
{
    private string $filename = '';

    private string $xmlns = XmlReader::NAMESPACES_SS;

    private string $xmlnsss = XmlReader::NAMESPACES_SS;

    protected function tearDown(): void
    {
        if ($this->filename !== '') {
            unlink($this->filename);
        }
    }

    public function testNumericEntities(): void
    {
        $this->filename = File::temporaryFilename();
        $entity_value = str_repeat('A', 100000);
        $refs = str_repeat('&big;', 200);

        $xml = <<<EOF
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            &#38;#60;!DOCTYPE Workbook [
                &#38;#60;!ENTITY big "$entity_value">
            ]>
            <Workbook
                xmlns="{$this->xmlns}"
                xmlns:ss="{$this->xmlnsss}"
            >
            <Worksheet ss:Name="Sheet1">
            <Table><Row><Cell>
                <Data ss:Type="String">$refs</Data>
            </Cell></Row></Table>
            </Worksheet>
            </Workbook>
            EOF;
        self::assertNotFalse(
            file_put_contents($this->filename, $xml)
        );
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Cannot load invalid XML file');
        $reader = new XmlReader();
        $reader->load($this->filename);
    }

    public function testDetectDoctype(): void
    {
        $this->filename = File::temporaryFilename();
        $entity_value = str_repeat('A', 100000);
        $refs = str_repeat('&big;', 200);

        $xml = <<<EOF
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <!DOCTYPE Workbook [
                &#38;#60;!ENTITY big "$entity_value">
            ]>
            <Workbook
                xmlns="{$this->xmlns}"
                xmlns:ss="{$this->xmlnsss}"
            >
            <Worksheet ss:Name="Sheet1">
            <Table><Row><Cell>
                <Data ss:Type="String">$refs</Data>
            </Cell></Row></Table>
            </Worksheet>
            </Workbook>
            EOF;
        self::assertNotFalse(
            file_put_contents($this->filename, $xml)
        );
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Detected use of ENTITY');
        $reader = new XmlReader();
        $reader->load($this->filename);
    }

    public function testNoDoctype(): void
    {
        $this->filename = File::temporaryFilename();
        $refs = str_repeat('&amp;&pi;&#x03cF;&#48;', 200);
        $refsOut = str_repeat('&πϏ0', 200);

        $xml = <<<EOF
            <?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook
                xmlns="{$this->xmlns}"
                xmlns:ss="{$this->xmlnsss}"
            >
            <Worksheet ss:Name="Sheet1">
            <Table><Row><Cell>
                <Data ss:Type="String">$refs</Data>
            </Cell></Row></Table>
            </Worksheet>
            </Workbook>
            EOF;
        self::assertNotFalse(
            file_put_contents($this->filename, $xml)
        );
        // no 'unentity' function in this branch
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Cannot load invalid XML file');
        $reader = new XmlReader();
        $spreadsheet = $reader->load($this->filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame($refsOut, $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}

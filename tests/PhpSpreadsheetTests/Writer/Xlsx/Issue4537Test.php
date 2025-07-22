<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\TextElement;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue4537Test extends TestCase
{
    private string $outputFilename = '';

    protected function tearDown(): void
    {
        if ($this->outputFilename !== '') {
            unlink($this->outputFilename);
            $this->outputFilename = '';
        }
    }

    public function testBackgroundImage(): void
    {
        $this->outputFilename = File::temporaryFilename();
        $testString = "\"He\": '<?>'";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValueExplicit($testString, DataType::TYPE_INLINE);
        $sheet->getCell('A2')->setValue($testString);
        $richText = new RichText();
        $richText->addText(new TextElement($testString));
        $sheet->getCell('A3')->setValue($richText);
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->outputFilename);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outputFilename);
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame($testString, $rsheet->getCell('A1')->getValueString());
        self::assertSame($testString, $rsheet->getCell('A2')->getValueString());
        self::assertSame($testString, $rsheet->getCell('A3')->getValueString());
        $reloadedSpreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFilename;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file);
        // expected, and expected1/2 below, do not escape apostrophes
        $expected = 't="inlineStr"><is><t>&quot;He&quot;: \'&lt;?&gt;\'</t></is>';
        if ($data === false) {
            self::fail('Unable to read worksheets file');
        } else {
            self::assertStringContainsString($expected, $data, 'inline string');
        }

        $file = 'zip://';
        $file .= $this->outputFilename;
        $file .= '#xl/sharedStrings.xml';
        $data = file_get_contents($file);
        $expected1 = '<t>&quot;He&quot;: \'&lt;?&gt;\'</t>';
        $expected2 = '<t xml:space="preserve">&quot;He&quot;: \'&lt;?&gt;\'</t>';
        if ($data === false) {
            self::fail('Unable to read sharedStrings file');
        } else {
            self::assertStringContainsString($expected1, $data, 'string');
            self::assertStringContainsString($expected2, $data, 'rich text');
        }
    }
}

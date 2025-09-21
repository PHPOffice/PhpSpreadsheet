<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\TextElement;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
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
        $writer = new OdsWriter($spreadsheet);
        $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $writer->save($this->outputFilename);
        $spreadsheet->disconnectWorksheets();

        $reader = new OdsReader();
        $reloadedSpreadsheet = $reader->load($this->outputFilename);
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame($testString, $rsheet->getCell('A1')->getValueString());
        self::assertSame($testString, $rsheet->getCell('A2')->getValueString());
        self::assertSame($testString, $rsheet->getCell('A3')->getValueString());
        $reloadedSpreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outputFilename;
        $file .= '#content.xml';
        $data = file_get_contents($file);
        // expected does not escape apostrophes
        $expected = '<text:p>&quot;He&quot;: \'&lt;?&gt;\'</text:p>';
        if ($data === false) {
            self::fail('Unable to read content file');
        } else {
            $count = substr_count($data, $expected);
            self::assertSame(3, $count);
        }
    }
}

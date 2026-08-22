<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class Issue2082Test extends TestCase
{
    public function testBoolWrite(): void
    {
        // Problem writing boolean function values is detected only when
        // opening spreadsheet in Excel 2007-2016. Test verifies that
        // value field is written correctly for those versions.
        $outputFilename = File::temporaryFilename();
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(['A', 'B', 'C', 'D']);
        $worksheet->getCell('A2')->setValue('=A1<>"A"');
        $worksheet->getCell('A3')->setValue('=A1="A"');
        $worksheet->getCell('B2')->setValue('=LEFT(B1, 0)');
        $worksheet->getCell('B3')->setValue('=B2=""');

        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $zipfile = "zip://$outputFilename#xl/worksheets/sheet1.xml";
        $contents = file_get_contents($zipfile);
        unlink($outputFilename);
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertStringContainsString('<c r="A2" t="b"><f>A1&lt;&gt;&quot;A&quot;</f><v>0</v></c>', $contents);
            self::assertStringContainsString('<c r="A3" t="b"><f>A1=&quot;A&quot;</f><v>1</v></c>', $contents);
            self::assertStringContainsString('<c r="B2" t="str"><f>LEFT(B1, 0)</f><v></v></c>', $contents);
            self::assertStringContainsString('<c r="B3" t="b"><f>B2=&quot;&quot;</f><v>1</v></c>', $contents);
        }
    }
}

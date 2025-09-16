<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class Issue4248Test extends TestCase
{
    private string $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testStyles(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.4248.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new XlsxWriter($spreadsheet);
        $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $this->outfile = File::temporaryFilename();
        $writer->save($this->outfile);
        $spreadsheet->disconnectWorksheets();

        $file = 'zip://';
        $file .= $this->outfile;
        $file .= '#xl/styles.xml';
        $data = file_get_contents($file) ?: '';
        $expected = '<fill>'
            . '<patternFill patternType="darkDown"/>'
            . '</fill>';
        self::assertStringContainsString($expected, $data, 'neither fgColor nor bgColor');
        $expected = '<fill>'
            . '<patternFill patternType="darkDown">'
            . '<bgColor rgb="FFBDD7EE"/>'
            . '</patternFill></fill>';
        self::assertStringContainsString($expected, $data, 'bgColor but no fgColor');
        $expected = '<dxfs count="15">'
            . '<dxf>' // dxfId 1 - fill color for Oui
            . '<fill>'
            . '<patternFill><bgColor rgb="FF00B050"/></patternFill>'
            . '</fill>'
            . '<border/>'
            . '</dxf>'
            . '<dxf>' // dxfId 2 - fill color for Non
            . '<font><color rgb="FF9C0006"/></font>'
            . '<fill>'
            . '<patternFill><bgColor rgb="FFFFC7CE"/></patternFill>'
            . '</fill>'
            . '<border/>'
            . '</dxf>';
        self::assertStringContainsString($expected, $data, 'conditional fill styles');

        $file = 'zip://';
        $file .= $this->outfile;
        $file .= '#xl/worksheets/sheet1.xml';
        $data = file_get_contents($file) ?: '';
        $expected = '<conditionalFormatting sqref="C16:C38 E17:H18 I17:J37 D18 J23:J38 E38 I38">'
            . '<cfRule type="containsText" dxfId="0" priority="15" operator="containsText" text="Oui">'
            . '<formula>NOT(ISERROR(SEARCH(&quot;Oui&quot;,C16)))</formula>'
            . '</cfRule>'
            . '</conditionalFormatting>';
        self::assertStringContainsString($expected, $data, 'first condition for D18');
        $expected = '<conditionalFormatting sqref="C16:C38 I17:J37 E17:H18 D18 J23:J38 E38 I38">'
            . '<cfRule type="containsText" dxfId="1" priority="14" operator="containsText" text="Non">'
            . '<formula>NOT(ISERROR(SEARCH(&quot;Non&quot;,C16)))</formula>'
            . '</cfRule>'
            . '</conditionalFormatting>';
        self::assertStringContainsString($expected, $data, 'second condition for D18');
    }

    public function testHtml(): void
    {
        $file = 'tests/data/Reader/XLSX/issue.4248.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);

        $file = 'zip://';
        $file .= $this->outfile;
        $file .= '#xl/styles.xml';
        $data = str_replace(["\r", "\n"], '', $writer->generateHtmlAll());
        $expected = '          <tr class="row17">' // Cell D18
            . '            <td class="column0 style0">&nbsp;</td>'
            . '            <td class="column1 style28 null"></td>'
            . '            <td class="column2 style35 s">Eligible </td>'
            . '            <td class="column3 style70 s">Non</td>';
        self::assertStringContainsString($expected, $data, 'Cell D18 style');
        $expected = '      td.style70, th.style70 { vertical-align:middle; text-align:center; border-bottom:1px solid #000000 !important; border-top:2px solid #000000 !important; border-left:2px solid #000000 !important; border-right:1px solid #000000 !important; font-weight:bold; color:#000000; font-family:\'Calibri\'; font-size:16pt; background-color:#BDD7EE }';
        self::assertStringContainsString($expected, $data, 'background color');

        $spreadsheet->disconnectWorksheets();
    }
}

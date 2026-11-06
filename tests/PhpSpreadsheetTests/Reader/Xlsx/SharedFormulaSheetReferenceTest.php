<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class SharedFormulaSheetReferenceTest extends TestCase
{
    private string $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    public function testSheetQualifiedReferencesAreShifted(): void
    {
        $this->outfile = File::temporaryFilename();
        self::createFile($this->outfile, [
            'B' => 'Data!A1*1',
            'C' => '&apos;My Data&apos;!A1*1',
            'D' => 'SUM(Data!A1:A2)',
            'E' => 'Data!$A$1',
            'F' => 'Data!$A1+Data!A$1',
            'G' => 'A1+Data!A1',
        ]);

        $spreadsheet = (new Xlsx())->load($this->outfile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('S');
        self::assertSame([
            [1, '=Data!A1*1', "='My Data'!A1*1", '=SUM(Data!A1:A2)', '=Data!$A$1', '=Data!$A1+Data!A$1', '=A1+Data!A1'],
            [2, '=Data!A2*1', "='My Data'!A2*1", '=SUM(Data!A2:A3)', '=Data!$A$1', '=Data!$A2+Data!A$1', '=A2+Data!A2'],
            [3, '=Data!A3*1', "='My Data'!A3*1", '=SUM(Data!A3:A4)', '=Data!$A$1', '=Data!$A3+Data!A$1', '=A3+Data!A3'],
        ], $sheet->toArray(null, false, false));
        self::assertSame([
            [1, 10, 100, 30, 10, 20, 11],
            [2, 20, 200, 50, 10, 30, 22],
            [3, 30, 300, 70, 10, 40, 33],
        ], $sheet->toArray(null, true, false));
        $spreadsheet->disconnectWorksheets();
    }

    public function testTextInQuotesIsNotShifted(): void
    {
        $this->outfile = File::temporaryFilename();
        self::createFile($this->outfile, [
            'B' => 'IF(Data!A1=&quot;A1&quot;,&quot;B2&quot;,&quot;Data!C3&quot;)',
            'C' => '&quot;12:30&quot;&amp;A1',
        ]);

        $spreadsheet = (new Xlsx())->load($this->outfile);
        $sheet = $spreadsheet->getSheetByNameOrThrow('S');
        self::assertSame([
            [1, '=IF(Data!A1="A1","B2","Data!C3")', '="12:30"&A1'],
            [2, '=IF(Data!A2="A1","B2","Data!C3")', '="12:30"&A2'],
            [3, '=IF(Data!A3="A1","B2","Data!C3")', '="12:30"&A3'],
        ], $sheet->toArray(null, false, false));
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Sheet S has 1, 2, 3 in column A and, for each entry of $formulas,
     * a formula shared by rows 1 to 3 of that column.
     *
     * @param array<string, string> $formulas formula in row 1 by column, XML-escaped
     */
    private static function createFile(string $filename, array $formulas): void
    {
        $rows = '';
        for ($row = 1; $row <= 3; ++$row) {
            $rows .= "<row r=\"$row\"><c r=\"A$row\"><v>$row</v></c>";
            $sharedIndex = 0;
            foreach ($formulas as $column => $formula) {
                $text = ($row === 1) ? $formula : '';
                $rows .= "<c r=\"$column$row\"><f t=\"shared\" ref=\"{$column}1:{$column}3\" si=\"$sharedIndex\">$text</f></c>";
                ++$sharedIndex;
            }
            $rows .= '</row>';
        }
        $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $relationships = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $packageRelationships = 'http://schemas.openxmlformats.org/package/2006/relationships';
        $values = static fn (int $factor): string => implode('', array_map(
            static fn (int $row): string => "<row r=\"$row\"><c r=\"A$row\"><v>" . ($row * $factor) . '</v></c></row>',
            [1, 2, 3, 4]
        ));

        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>');
        $zip->addFromString('_rels/.rels', "<?xml version=\"1.0\"?><Relationships xmlns=\"$packageRelationships\">"
            . "<Relationship Id=\"rId1\" Type=\"$relationships/officeDocument\" Target=\"xl/workbook.xml\"/>"
            . '</Relationships>');
        $zip->addFromString('xl/workbook.xml', "<?xml version=\"1.0\"?><workbook xmlns=\"$main\" xmlns:r=\"$relationships\"><sheets>"
            . '<sheet name="S" sheetId="1" r:id="rId1"/><sheet name="Data" sheetId="2" r:id="rId2"/><sheet name="My Data" sheetId="3" r:id="rId3"/>'
            . '</sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', "<?xml version=\"1.0\"?><Relationships xmlns=\"$packageRelationships\">"
            . "<Relationship Id=\"rId1\" Type=\"$relationships/worksheet\" Target=\"worksheets/sheet1.xml\"/>"
            . "<Relationship Id=\"rId2\" Type=\"$relationships/worksheet\" Target=\"worksheets/sheet2.xml\"/>"
            . "<Relationship Id=\"rId3\" Type=\"$relationships/worksheet\" Target=\"worksheets/sheet3.xml\"/>"
            . "<Relationship Id=\"rId4\" Type=\"$relationships/styles\" Target=\"styles.xml\"/>"
            . '</Relationships>');
        $zip->addFromString('xl/styles.xml', "<?xml version=\"1.0\"?><styleSheet xmlns=\"$main\">"
            . '<fonts count="1"><font/></fonts><fills count="1"><fill/></fills><borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="1"><xf/></cellXfs>'
            . '</styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', "<?xml version=\"1.0\"?><worksheet xmlns=\"$main\"><sheetData>$rows</sheetData></worksheet>");
        $zip->addFromString('xl/worksheets/sheet2.xml', "<?xml version=\"1.0\"?><worksheet xmlns=\"$main\"><sheetData>" . $values(10) . '</sheetData></worksheet>');
        $zip->addFromString('xl/worksheets/sheet3.xml', "<?xml version=\"1.0\"?><worksheet xmlns=\"$main\"><sheetData>" . $values(100) . '</sheetData></worksheet>');
        $zip->close();
    }
}

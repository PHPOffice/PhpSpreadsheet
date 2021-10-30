<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NamespaceIssue2109bTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/issue2109b.xlsx';

    public function testPreliminaries(): void
    {
        $file = 'zip://';
        $file .= self::$testbook;
        $file .= '#xl/workbook.xml';
        $data = file_get_contents($file);
        // confirm that file contains expected namespaced xml tag
        if ($data === false) {
            self::fail('Unable to read file');
        } else {
            self::assertStringContainsString('<x:workbook ', $data);
        }
    }

    public function testInfo(): void
    {
        $reader = new Xlsx();
        $workSheetInfo = $reader->listWorkSheetInfo(self::$testbook);
        $info0 = $workSheetInfo[0];
        self::assertEquals('Sheet1', $info0['worksheetName']);
        self::assertEquals('AF', $info0['lastColumnLetter']);
        self::assertEquals(31, $info0['lastColumnIndex']);
        self::assertEquals(4, $info0['totalRows']);
        self::assertEquals(32, $info0['totalColumns']);
    }

    public function testSheetNames(): void
    {
        $reader = new Xlsx();
        $worksheetNames = $reader->listWorksheetNames(self::$testbook);
        self::assertEquals(['Sheet1'], $worksheetNames);
    }

    public function testActive(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Sheet1', $sheet->getTitle());
        self::assertNull($sheet->getFreezePane());
        self::assertNull($sheet->getTopLeftCell());
        self::assertSame('A1', $sheet->getSelectedCells());
        $spreadsheet->disconnectWorksheets();
    }

    private static function getCellValue(Worksheet $sheet, string $cell): string
    {
        $result = $sheet->getCell($cell)->getValue();

        return (string) $result;
    }

    public function testLoadXlsx(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getSheet(0);
        self::assertEquals('Sheet1', $sheet->getTitle());
        $expectedArray = [
            'A1' => 'Channel Name = Cartoon Network RSE',
            'B2' => 'Event ID',
            'C3' => '2021-05-17 03:00',
            'F4' => 'The Internet',
            'AF3' => '902476',
            'AF4' => '902477',
            'J2' => 'Episode Synopsis',
            'J3' => 'Gumball and Darwin\'s reputation is challenged and they really couldn\'t care less...',
            'J4' => 'Gumball accidentally uploads a video of himself and wants it gone.',
        ];
        foreach ($expectedArray as $key => $value) {
            self::assertSame($value, self::getCellValue($sheet, $key), "error in cell $key");
        }
        $spreadsheet->disconnectWorksheets();
    }
}

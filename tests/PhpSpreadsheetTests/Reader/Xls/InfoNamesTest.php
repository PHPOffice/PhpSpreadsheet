<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\CodePage;
use PHPUnit\Framework\TestCase;

class InfoNamesTest extends TestCase
{
    public function testWorksheetNamesBiff5(): void
    {
        $filename = 'samples/templates/30templatebiff5.xls';
        $reader = new Xls();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Invoice', 'Terms and conditions'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff5(): void
    {
        $filename = 'samples/templates/30templatebiff5.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Invoice',
                'lastColumnLetter' => 'E',
                'lastColumnIndex' => 4,
                'totalRows' => 19,
                'totalColumns' => 5,
            ],
            [
                'worksheetName' => 'Terms and conditions',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 3,
                'totalColumns' => 2,
            ],
        ];
        self::assertSame($expected, $info);
        self::assertSame(Xls::XLS_BIFF7, $reader->getVersion());
        self::assertSame('CP1252', $reader->getCodepage());
    }

    public function testWorksheetNamesBiff8(): void
    {
        $filename = 'samples/templates/31docproperties.xls';
        $reader = new Xls();
        $names = $reader->listWorksheetNames($filename);
        $expected = ['Worksheet'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff8(): void
    {
        $filename = 'samples/templates/31docproperties.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Worksheet',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 1,
                'totalColumns' => 2,
            ],
        ];
        self::assertSame($expected, $info);
        self::assertSame(Xls::XLS_BIFF8, $reader->getVersion());
        self::assertSame('UTF-16LE', $reader->getCodepage());
    }

    /**
     * Test load Xls file with MACCENTRALEUROPE encoding, which is implemented
     * as MAC-CENTRALEUROPE on some systems. Issue #549.
     */
    private const MAC_CE = ['MACCENTRALEUROPE', 'MAC-CENTRALEUROPE'];

    private const MAC_FILE5 = 'tests/data/Reader/XLS/maccentraleurope.biff5.xls';
    private const MAC_FILE8 = 'tests/data/Reader/XLS/maccentraleurope.xls';

    public function testWorksheetNamesBiff5Mac(): void
    {
        $codePages = CodePage::getEncodings();
        self::assertSame(self::MAC_CE, $codePages[10029]);
        $reader = new Xls();
        $names = $reader->listWorksheetNames(self::MAC_FILE5);
        $expected = ['Ärkusz1'];
        self::assertSame($expected, $names);
    }

    public function testWorksheetInfoBiff5Mac(): void
    {
        $codePages = CodePage::getEncodings();
        // prior test has replaced array with single string
        self::assertContains($codePages[10029], self::MAC_CE);
        $reader = new Xls();
        $info = $reader->listWorksheetInfo(self::MAC_FILE5);
        $expected = [
            [
                'worksheetName' => 'Ärkusz1',
                'lastColumnLetter' => 'P',
                'lastColumnIndex' => 15,
                'totalRows' => 3,
                'totalColumns' => 16,
            ],
        ];
        self::assertSame($expected, $info);
        self::assertSame(Xls::XLS_BIFF7, $reader->getVersion());
        self::assertContains($reader->getCodepage(), self::MAC_CE);
    }

    public function testLoadMacCentralEuropeBiff5(): void
    {
        $reader = new Xls();
        $spreadsheet = $reader->load(self::MAC_FILE5);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Ärkusz1', $sheet->getTitle());
        self::assertSame('Ładowność', $sheet->getCell('I1')->getValue());
        self::assertSame(Xls::XLS_BIFF7, $reader->getVersion());
        self::assertContains($reader->getCodepage(), self::MAC_CE);
        $spreadsheet->disconnectWorksheets();
    }

    public function testLoadMacCentralEuropeBiff8(): void
    {
        // Document is UTF-16LE as a whole,
        //    but some strings are stored as MACCENTRALEUROPE
        $reader = new Xls();
        $spreadsheet = $reader->load(self::MAC_FILE8);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame('Arkusz1', $sheet->getTitle());
        self::assertSame('Ładowność', $sheet->getCell('I1')->getValue());
        self::assertSame(Xls::XLS_BIFF8, $reader->getVersion());
        self::assertSame('UTF-16LE', $reader->getCodepage());
        $properties = $spreadsheet->getProperties();
        // the following is stored as MACCENTRALEUROPE, not UTF-16LE
        self::assertSame('Użytkownik Microsoft Office', $properties->getLastModifiedBy());
        $spreadsheet->disconnectWorksheets();
    }
}

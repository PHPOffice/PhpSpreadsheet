<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
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
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Terms and conditions',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 3,
                'totalColumns' => 2,
                'sheetState' => 'visible',
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
                'sheetState' => 'visible',
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
                'sheetState' => 'visible',
            ],
        ];
        self::assertSame($expected, $info);
        self::assertSame(Xls::XLS_BIFF7, $reader->getVersion());
        self::assertContains($reader->getCodepage(), self::MAC_CE);
    }

    public function testWorksheetNamesBiff5Special(): void
    {
        // Sadly, no unit test was added for PR #1484,
        // so we have to invent a fake one now.
        $reader = new XlsSpecialCodePage();
        $reader->setCodePage('CP855'); // use Cyrillic rather than MACCENTRAL
        $names = $reader->listWorksheetNames(self::MAC_FILE5);
        $expected = ['ђrkusz1']; // first character interpreted as Cyrillic
        self::assertSame($expected, $names);
    }

    public function testBadCodePage(): void
    {
        $this->expectException(PhpSpreadsheetException::class);
        $this->expectExceptionMessage('Unknown codepage');
        $reader = new Xls();
        $reader->setCodePage('XXXCP855');
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

    public function testDimensions(): void
    {
        $filename = 'tests/data/Reader/XLS/pr.4687.excel.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'D',
                'lastColumnIndex' => 3,
                'totalRows' => 2,
                'totalColumns' => 4,
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Sheet2',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 4,
                'totalColumns' => 2,
                'sheetState' => 'visible',
            ],
        ];
        self::assertSame($expected, $info);
        $info = $reader->listWorksheetDimensions($filename);
        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'dimensionsMinR' => 0,
                'dimensionsMaxR' => 2,
                'dimensionsMinC' => 0,
                'dimensionsMaxC' => 4,
                'lastColumnLetter' => 'D',
            ],
            [
                'worksheetName' => 'Sheet2',
                'dimensionsMinR' => 0,
                'dimensionsMaxR' => 4,
                'dimensionsMinC' => 0,
                'dimensionsMaxC' => 2,
                'lastColumnLetter' => 'B',
            ],
        ];
        self::assertSame($expected, $info);
    }

    public function testChartSheetIgnored(): void
    {
        $filename = 'tests/data/Reader/XLS/chartsheet.xls';
        $reader = new Xls();
        $info = $reader->listWorksheetInfo($filename);
        $expected = [
            [
                'worksheetName' => 'Data',
                'lastColumnLetter' => 'M',
                'lastColumnIndex' => 12,
                'totalRows' => 7,
                'totalColumns' => 13,
                'sheetState' => 'visible',
            ],
        ];
        self::assertSame($expected, $info);
        $info = $reader->listWorksheetDimensions($filename);
        $expected = [
            [
                'worksheetName' => 'Data',
                'dimensionsMinR' => 0,
                'dimensionsMaxR' => 7,
                'dimensionsMinC' => 0,
                'dimensionsMaxC' => 13,
                'lastColumnLetter' => 'M',
            ],
        ];
        self::assertSame($expected, $info);
    }
}

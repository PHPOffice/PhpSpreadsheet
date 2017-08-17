<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PHPUnit_Framework_TestCase;

class WorksheetTest extends PHPUnit_Framework_TestCase
{
    public function testSetTitle()
    {
        $testTitle = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setTitle($testTitle);
        $this->assertSame($testTitle, $worksheet->getTitle());
    }

    public function setTitleInvalidProvider()
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet title.'],
            ['invalid*title', 'Invalid character found in sheet title'],
        ];
    }

    /**
     * @param string $title
     * @param string $expectMessage
     * @dataProvider setTitleInvalidProvider
     */
    public function testSetTitleInvalid($title, $expectMessage)
    {
        // First, test setting title with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setTitle($title, true, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setTitle($title);
    }

    public function testSetTitleDuplicate()
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique title -- should be unchanged
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setTitle('Test Title');
        $this->assertSame('Test Title', $sheet->getTitle());

        // Set duplicate title -- should have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setTitle('Test Title');
        $this->assertSame('Test Title 1', $sheet->getTitle());

        // Set duplicate title with validation disabled -- should be unchanged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setTitle('Test Title', true, false);
        $this->assertSame('Test Title', $sheet->getTitle());
    }

    public function testSetCodeName()
    {
        $testCodeName = str_repeat('a', 31);

        $worksheet = new Worksheet();
        $worksheet->setCodeName($testCodeName);
        $this->assertSame($testCodeName, $worksheet->getCodeName());
    }

    public function setCodeNameInvalidProvider()
    {
        return [
            [str_repeat('a', 32), 'Maximum 31 characters allowed in sheet code name.'],
            ['invalid*code*name', 'Invalid character found in sheet code name'],
        ];
    }

    /**
     * @param string $codeName
     * @param string $expectMessage
     * @dataProvider setCodeNameInvalidProvider
     */
    public function testSetCodeNameInvalid($codeName, $expectMessage)
    {
        // First, test setting code name with validation disabled -- should be successful
        $worksheet = new Worksheet();
        $worksheet->setCodeName($codeName, false);

        // Next, test again with validation enabled -- this time we should fail
        $worksheet = new Worksheet();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectMessage);
        $worksheet->setCodeName($codeName);
    }

    public function testSetCodeNameDuplicate()
    {
        // Create a Spreadsheet with three Worksheets (the first is created automatically)
        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $spreadsheet->createSheet();

        // Set unique code name -- should be massaged to Snake_Case
        $sheet = $spreadsheet->getSheet(0);
        $sheet->setCodeName('Test Code Name');
        $this->assertSame('Test_Code_Name', $sheet->getCodeName());

        // Set duplicate code name -- should be massaged and have numeric suffix appended
        $sheet = $spreadsheet->getSheet(1);
        $sheet->setCodeName('Test Code Name');
        $this->assertSame('Test_Code_Name_1', $sheet->getCodeName());

        // Set duplicate code name with validation disabled -- should be unchanged, and unmassaged
        $sheet = $spreadsheet->getSheet(2);
        $sheet->setCodeName('Test Code Name', false);
        $this->assertSame('Test Code Name', $sheet->getCodeName());
    }
}

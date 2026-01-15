<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

/**
 * @TODO The class doesn't read the bold/italic/underline properties (rich text)
 */
class OdsInfoTest extends TestCase
{
    public function testReadFileProperties(): void
    {
        $filename = 'tests/data/Reader/Ods/data.ods';

        // Load into this instance
        $reader = new Ods();

        // Test "listWorksheetNames" method

        self::assertSame([
            'Sheet1',
            'Second Sheet',
        ], $reader->listWorksheetNames($filename));
    }

    public function testNoMimeType(): void
    {
        $filename = 'tests/data/Reader/Ods/nomimetype.ods';

        // Load into this instance
        $reader = new Ods();

        self::assertTrue($reader->canRead($filename));
    }

    public function testReadBadFileProperties(): void
    {
        $this->expectException(ReaderException::class);

        // Load into this instance
        $reader = new Ods();

        // Test "listWorksheetNames" method

        self::assertSame([
            'Sheet1',
            'Second Sheet',
        ], $reader->listWorksheetNames(__FILE__));
    }

    public function testReadFileInfo(): void
    {
        $filename = 'tests/data/Reader/Ods/data.ods';
        $reader = new Ods();
        $wsinfo = $reader->listWorkSheetInfo($filename);
        self::assertSame([
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'C',
                'lastColumnIndex' => 2,
                'totalRows' => 12,
                'totalColumns' => 3,
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Second Sheet',
                'lastColumnLetter' => 'B',
                'lastColumnIndex' => 1,
                'totalRows' => 2,
                'totalColumns' => 2,
                'sheetState' => 'visible',
            ],
        ], $wsinfo);
    }

    public function testReadBadFileInfo(): void
    {
        $this->expectException(ReaderException::class);
        $filename = __FILE__;
        $reader = new Ods();
        $wsinfo = $reader->listWorkSheetInfo($filename);
    }

    public function testReadFileInfoWithEmpties(): void
    {
        $filename = 'tests/data/Reader/Ods/RepeatedCells.ods';
        $reader = new Ods();
        $wsinfo = $reader->listWorkSheetInfo($filename);
        self::assertSame([
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'K',
                'lastColumnIndex' => 10,
                'totalRows' => 1,
                'totalColumns' => 11,
                'sheetState' => 'visible',
            ],
        ], $wsinfo);
    }

    public function testOneMoreWorksheetInfo(): void
    {
        $filename = 'tests/data/Reader/Ods/issue.4528.ods';
        $reader = new Ods();
        $wsinfo = $reader->listWorkSheetInfo($filename);
        self::assertSame([
            [
                'worksheetName' => 'Francais',
                'lastColumnLetter' => 'AZ',
                'lastColumnIndex' => 51,
                'totalRows' => 811,
                'totalColumns' => 52,
                'sheetState' => 'visible',
            ],
        ], $wsinfo);
    }
}

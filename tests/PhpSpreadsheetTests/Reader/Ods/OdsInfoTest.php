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

        self::assertEquals([
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

        self::assertEquals([
            'Sheet1',
            'Second Sheet',
        ], $reader->listWorksheetNames(__FILE__));
    }

    public function testReadFileInfo(): void
    {
        $filename = 'tests/data/Reader/Ods/data.ods';

        // Load into this instance
        $reader = new Ods();

        // Test "listWorksheetNames" method

        $wsinfo = $reader->listWorkSheetInfo($filename);
        self::assertEquals([
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'C',
                'lastColumnIndex' => 2,
                'totalRows' => 12,
                'totalColumns' => 3,
            ],
            [
                'worksheetName' => 'Second Sheet',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 2,
                'totalColumns' => 1,
            ],
        ], $wsinfo);
    }

    public function testReadBadFileInfo(): void
    {
        $this->expectException(ReaderException::class);
        $filename = __FILE__;

        // Load into this instance
        $reader = new Ods();

        // Test "listWorksheetNames" method

        $wsinfo = $reader->listWorkSheetInfo($filename);
        self::assertEquals([
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'C',
                'lastColumnIndex' => 2,
                'totalRows' => 11,
                'totalColumns' => 3,
            ],
            [
                'worksheetName' => 'Second Sheet',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 2,
                'totalColumns' => 1,
            ],
        ], $wsinfo);
    }
}

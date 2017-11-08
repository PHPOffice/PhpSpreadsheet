<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetUseUploadTempDirectory()
    {
        $expectedResult = false;

        $result = File::getUseUploadTempDirectory();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetUseUploadTempDirectory()
    {
        $useUploadTempDirectoryValues = [
            true,
            false,
        ];

        foreach ($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
            File::setUseUploadTempDirectory($useUploadTempDirectoryValue);

            $result = File::getUseUploadTempDirectory();
            self::assertEquals($useUploadTempDirectoryValue, $result);
        }
    }
}

<?php

namespace PhpSpreadsheetTests\Shared;

use PhpSpreadsheet\Shared\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUseUploadTempDirectory()
    {
        $expectedResult = false;

        $result = File::getUseUploadTempDirectory();
        $this->assertEquals($expectedResult, $result);
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
            $this->assertEquals($useUploadTempDirectoryValue, $result);
        }
    }
}

<?php

namespace PhpSpreadsheetTests\Shared;

use PhpSpreadsheet\Shared\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUseUploadTempDirectory()
    {
        $expectedResult = false;

        $result = call_user_func([File::class, 'getUseUploadTempDirectory']);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetUseUploadTempDirectory()
    {
        $useUploadTempDirectoryValues = [
            true,
            false,
        ];

        foreach ($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
            call_user_func([File::class, 'setUseUploadTempDirectory'], $useUploadTempDirectoryValue);

            $result = call_user_func([File::class, 'getUseUploadTempDirectory']);
            $this->assertEquals($useUploadTempDirectoryValue, $result);
        }
    }
}

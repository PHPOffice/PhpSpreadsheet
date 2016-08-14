<?php

namespace PhpSpreadsheet\Tests\Shared;

use PHPExcel\Shared\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUseUploadTempDirectory()
    {
        $expectedResult = false;

        $result = call_user_func(array(File::class,'getUseUploadTempDirectory'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetUseUploadTempDirectory()
    {
        $useUploadTempDirectoryValues = array(
            true,
            false,
        );

        foreach ($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
            call_user_func(array(File::class,'setUseUploadTempDirectory'), $useUploadTempDirectoryValue);

            $result = call_user_func(array(File::class,'getUseUploadTempDirectory'));
            $this->assertEquals($useUploadTempDirectoryValue, $result);
        }
    }
}

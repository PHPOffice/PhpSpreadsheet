<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private bool $uploadFlag = false;

    private string $tempfile = '';

    protected function setUp(): void
    {
        $this->uploadFlag = File::getUseUploadTempDirectory();
    }

    protected function tearDown(): void
    {
        File::setUseUploadTempDirectory($this->uploadFlag);
        if ($this->tempfile !== '') {
            unlink($this->tempfile);
            $this->tempfile = '';
        }
    }

    public function testGetUseUploadTempDirectory(): void
    {
        $expectedResult = false;

        $result = File::getUseUploadTempDirectory();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetUseUploadTempDirectory(): void
    {
        $useUploadTempDirectoryValues = [
            true,
            false,
        ];
        $temp = ini_get('upload_tmp_dir') ?: '';
        $badArray = ['', sys_get_temp_dir()];

        foreach ($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
            File::setUseUploadTempDirectory($useUploadTempDirectoryValue);

            $result = File::getUseUploadTempDirectory();
            self::assertEquals($useUploadTempDirectoryValue, $result);
            $result = File::sysGetTempDir();
            if (!$useUploadTempDirectoryValue || in_array($temp, $badArray, true)) {
                self::assertSame(realpath(sys_get_temp_dir()), $result);
            } else {
                self::assertSame(realpath($temp), $result);
            }
        }
    }

    public function testUploadTmpDir(): void
    {
        $temp = ini_get('upload_tmp_dir') ?: '';
        $badArray = ['', sys_get_temp_dir()];
        if (in_array($temp, $badArray, true)) {
            self::markTestSkipped('upload_tmp_dir setting unusable for this test');
        } else {
            File::setUseUploadTempDirectory(true);
            $result = File::sysGetTempDir();
            self::assertSame(realpath($temp), $result);
        }
    }

    public function testNotExists(): void
    {
        $temp = File::temporaryFileName();
        file_put_contents($temp, '');
        File::assertFile($temp);
        self::assertTrue(File::testFileNoThrow($temp));
        unlink($temp);
        self::assertFalse(File::testFileNoThrow($temp));
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('does not exist');
        File::assertFile($temp);
    }

    public function testNotReadable(): void
    {
        if (PHP_OS_FAMILY === 'Windows' || stristr(PHP_OS, 'CYGWIN') !== false) {
            self::markTestSkipped('chmod does not work reliably on Windows');
        }
        $this->tempfile = $temp = File::temporaryFileName();
        file_put_contents($temp, '');
        if (chmod($temp, 7 * 8) === false) { // octal 070
            self::markTestSkipped('chmod failed');
        }
        self::assertFalse(File::testFileNoThrow($temp));
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('for reading');
        File::assertFile($temp);
    }

    public function testZip(): void
    {
        $temp = 'samples/templates/26template.xlsx';
        File::assertFile($temp, 'xl/workbook.xml');
        self::assertTrue(File::testFileNoThrow($temp, 'xl/workbook.xml'));
        self::assertFalse(File::testFileNoThrow($temp, 'xl/xworkbook.xml'));
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Could not find zip member');
        File::assertFile($temp, 'xl/xworkbook.xml');
    }
}

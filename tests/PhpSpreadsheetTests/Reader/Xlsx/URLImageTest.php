<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Reader\Utility\File;
use PHPUnit\Framework\TestCase;

class URLImageTest extends TestCase
{
    public function testURLImageSource(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath(__DIR__ . '/../../../data/Reader/XLSX/urlImage.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(1, $collection);

        foreach ($collection as $drawing) {
            self::assertInstanceOf(Drawing::class, $drawing);
            // Check if the source is a URL or a file path
            self::assertTrue($drawing->getIsURL());
            self::assertSame('https://www.globalipmanager.com/DataFiles/Pics/20/Berniaga.comahp2.jpg', $drawing->getPath());
            $imageContents = file_get_contents($drawing->getPath());
            self::assertNotFalse($imageContents);
            $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
            self::assertNotFalse($filePath);
            self::assertNotFalse(file_put_contents($filePath, $imageContents));
            $mimeType = mime_content_type($filePath);
            unlink($filePath);
            self::assertNotFalse($mimeType);
            $extension = File::mime2ext($mimeType);
            self::assertSame('jpeg', $extension);
        }
    }
}

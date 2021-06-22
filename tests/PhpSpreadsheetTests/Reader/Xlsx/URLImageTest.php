<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheetTests\Reader\Utility\File;
use PHPUnit\Framework\TestCase;

class URLImageTest extends TestCase
{
    public function testURLImageSource(): void
    {
        $filename = realpath(__DIR__ . '/../../../data/Reader/XLSX/urlImage.xlsx');
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getDrawingCollection() as $drawing) {
            if ($drawing instanceof MemoryDrawing) {
                // Skip memory drawings
            }
            elseif ($drawing->getPath()) {
                // Check if the source is a URL or a file path
                if ($drawing->getIsURL()) {
                    $imageContents = file_get_contents($drawing->getPath());
                    $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                    file_put_contents($filePath , $imageContents);
                    $mimeType = mime_content_type($filePath);
                    // You could use the below to find the extension from mime type.
                    $extension = File::mime2ext($mimeType);
                    self::assertEquals('jpeg', $extension);
                    unlink($filePath);
                }
                else {
                    self::fail('Could not assert that the file contains an image that is URL sourced.');
                }
            }
            else {
                self::fail('No image path found.');
            }
        }

        if (empty($worksheet->getDrawingCollection())) {
            self::fail('No image found in file.');
        }
    }
}

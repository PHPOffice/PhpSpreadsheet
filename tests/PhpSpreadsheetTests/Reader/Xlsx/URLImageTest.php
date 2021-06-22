<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheetTests\Reader\Utility\File;
use PHPUnit\Framework\TestCase;

class URLImageTest extends TestCase
{
    public function testURLImageSource(): void
    {
        $filename = realpath(__DIR__ . '/../../../data/Reader/XLSX/urlImage.xlsx');
        if (!$filename) {
            self::fail('No test file found.');
        } else {
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filename);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getDrawingCollection() as $drawing) {
                if ($drawing instanceof MemoryDrawing) {
                    // Skip memory drawings
                } elseif ($drawing instanceof Drawing) {
                    // Check if the source is a URL or a file path
                    if ($drawing->getPath() && $drawing->getIsURL()) {
                        $imageContents = file_get_contents($drawing->getPath());
                        $filePath = tempnam(sys_get_temp_dir(), 'Drawing');
                        if ($filePath) {
                            file_put_contents($filePath, $imageContents);
                            if (file_exists($filePath)) {
                                $mimeType = mime_content_type($filePath);
                                // You could use the below to find the extension from mime type.
                                if ($mimeType) {
                                    $extension = File::mime2ext($mimeType);
                                    self::assertEquals('jpeg', $extension);
                                    unlink($filePath);
                                } else {
                                    self::fail('Could establish mime type.');
                                }
                            } else {
                                self::fail('Could not write file to disk.');
                            }
                        } else {
                            self::fail('Could not create fiel path.');
                        }
                    } else {
                        self::fail('Could not assert that the file contains an image that is URL sourced.');
                    }
                } else {
                    self::fail('No image path found.');
                }
            }

            if (empty($worksheet->getDrawingCollection())) {
                self::fail('No image found in file.');
            }
        }
    }
}

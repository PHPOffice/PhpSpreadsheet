<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use GdImage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class MemoryDrawingTest extends AbstractFunctional
{
    private string $outfile = '';

    protected function tearDown(): void
    {
        if ($this->outfile !== '') {
            unlink($this->outfile);
            $this->outfile = '';
        }
    }

    /**
     * Test save and load XLSX file with transparent png.
     */
    public function testIssue3624(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $contents = file_get_contents('tests/data/Writer/XLSX/issue.3624b.png');
        $stamp = MemoryDrawing::fromString("$contents");
        $stamp->setName('Stamp');
        $stamp->setHeight(120);
        $stamp->setCoordinates('A2');
        $stamp->setWorksheet($sheet);
        $this->outfile = File::temporaryFilename();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->outfile);
        $spreadsheet->disconnectWorksheets();

        $reader = new XlsxReader();
        $reloadedSpreadsheet = $reader->load($this->outfile);
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $drawings = $rsheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        foreach ($drawings as $drawing) {
            if ($drawing instanceof Drawing) {
                $path = $drawing->getPath();
                $contents = file_get_contents($path);
                $gdImage = imagecreatefromstring("$contents");
                if ($gdImage === false) {
                    self::fail('unexpected failure in imagecreatefromstring');
                } else {
                    self::assertTrue(self::checkTransparent($gdImage));
                }
            } else {
                self::fail('Unexpected drawing not in Drawing class');
            }
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * Determine if image uses transparency.
     *
     * @see https://stackoverflow.com/questions/5495275/how-to-check-if-a-png-image-has-transparency-using-gd
     */
    private static function checkTransparent(GdImage $im): bool
    {
        $width = imagesx($im); // Get the width of the image
        $height = imagesy($im); // Get the height of the image

        // We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
        for ($i = 0; $i < $width; ++$i) {
            for ($j = 0; $j < $height; ++$j) {
                $rgba = imagecolorat($im, $i, $j);
                if (($rgba & 0x7F000000) >> 24) {
                    return true;
                }
            }
        }

        // If we dont find any pixel the function will return false.
        return false;
    }
}

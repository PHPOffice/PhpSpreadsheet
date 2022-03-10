<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingsTest extends AbstractFunctional
{
    /**
     * Test save and load XLSX file with drawing on 2nd worksheet.
     */
    public function testSaveLoadWithDrawingOn2ndWorksheet(): void
    {
        // Read spreadsheet from file
        $inputFilename = 'tests/data/Writer/XLSX/drawing_on_2nd_page.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($inputFilename);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test save and load XLSX file with drawing with the same file name.
     */
    public function testSaveLoadWithDrawingWithSamePath(): void
    {
        // Read spreadsheet from file
        $originalFileName = 'tests/data/Writer/XLSX/saving_drawing_with_same_path.xlsx';

        $originalFile = file_get_contents($originalFileName);

        $tempFileName = File::sysGetTempDir() . '/saving_drawing_with_same_path';

        file_put_contents($tempFileName, $originalFile);

        $reader = new Xlsx();
        $spreadsheet = $reader->load($tempFileName);

        $spreadsheet->getActiveSheet()->setCellValue('D5', 'foo');
        // Save spreadsheet to file to the same path. Success test case won't
        // throw exception here
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFileName);

        $reloadedSpreadsheet = $reader->load($tempFileName);

        unlink($tempFileName);

        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test save and load XLSX file with drawing in comment.
     */
    public function testSaveLoadWithDrawingInComment(): void
    {
        // Read spreadsheet from file
        $originalFileName = 'tests/data/Writer/XLSX/drawing_in_comment.xlsx';

        $originalFile = file_get_contents($originalFileName);

        $tempFileName = File::sysGetTempDir() . '/drawing_in_comment.xlsx';

        file_put_contents($tempFileName, $originalFile);

        // Load native xlsx file with drawing in comment background
        $reader = new Xlsx();
        $spreadsheet = $reader->load($tempFileName);

        $sheet = $spreadsheet->getActiveSheet();
        $comment = $sheet->getComment('A1');

        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 178);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 140);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_JPEG);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFileName);

        $reloadedSpreadsheet = $reader->load($tempFileName);
        unlink($tempFileName);

        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test save and load XLSX file with drawing in comment, image in BMP/GIF format saved as PNG.
     */
    public function testDrawingInCommentImageFormatsConversions(): void
    {
        $reader = new Xlsx();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add gif image to comment background
        $sheet->setCellValue('A1', '.gif');
        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setPath('tests/data/Writer/XLSX/green_square.gif');
        self::assertEquals($drawing->getWidth(), 150);
        self::assertEquals($drawing->getHeight(), 150);
        $comment = $sheet->getComment('A1');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '112.5pt');
        self::assertEquals($comment->getHeight(), '112.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_GIF);

        // Add bmp image to comment background
        $sheet->setCellValue('A2', '.bmp 16 colors');
        $drawing = new Drawing();
        $drawing->setName('Yellow Square');
        $drawing->setPath('tests/data/Writer/XLSX/yellow_square_16.bmp');
        self::assertEquals($drawing->getWidth(), 70);
        self::assertEquals($drawing->getHeight(), 70);
        $comment = $sheet->getComment('A2');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_BMP);

        // Write file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFileName = File::sysGetTempDir() . '/drawings_in_comments_conversions.xlsx';
        $writer->save($tempFileName);

        // Read new file
        $reloadedSpreadsheet = $reader->load($tempFileName);
        $sheet = $reloadedSpreadsheet->getActiveSheet();

        // Check first image in comment background
        $comment = $sheet->getComment('A1');
        self::assertEquals($comment->getWidth(), '112.5pt');
        self::assertEquals($comment->getHeight(), '112.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 150);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 150);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check second image in comment background
        $comment = $sheet->getComment('A2');
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 70);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 70);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        unlink($tempFileName);
        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test build and save XLSX with drawings in comments with comment size correction.
     */
    public function testBuildWithDifferentImageFormats(): void
    {
        $reader = new Xlsx();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add png image to comment background
        $sheet->setCellValue('A1', '.png');
        $drawing = new Drawing();
        $drawing->setName('Blue Square');
        $drawing->setPath('tests/data/Writer/XLSX/blue_square.png');
        self::assertEquals($drawing->getWidth(), 100);
        self::assertEquals($drawing->getHeight(), 100);
        $comment = $sheet->getComment('A1');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '75pt');
        self::assertEquals($comment->getHeight(), '75pt');

        $comment = $sheet->getComment('A1');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Add gif image to comment background
        $sheet->setCellValue('A2', '.gif');
        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setPath('tests/data/Writer/XLSX/green_square.gif');
        self::assertEquals($drawing->getWidth(), 150);
        self::assertEquals($drawing->getHeight(), 150);
        $comment = $sheet->getComment('A2');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '112.5pt');
        self::assertEquals($comment->getHeight(), '112.5pt');

        $comment = $sheet->getComment('A2');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_GIF);

        // Add jpeg image to comment background
        $sheet->setCellValue('A3', '.jpeg');
        $drawing = new Drawing();
        $drawing->setName('Red Square');
        $drawing->setPath('tests/data/Writer/XLSX/red_square.jpeg');
        self::assertEquals($drawing->getWidth(), 50);
        self::assertEquals($drawing->getHeight(), 50);
        $comment = $sheet->getComment('A3');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '37.5pt');
        self::assertEquals($comment->getHeight(), '37.5pt');

        $comment = $sheet->getComment('A3');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_JPEG);

        // Add bmp image to comment background
        $sheet->setCellValue('A4', '.bmp 16 colors');
        $drawing = new Drawing();
        $drawing->setName('Yellow Square');
        $drawing->setPath('tests/data/Writer/XLSX/yellow_square_16.bmp');
        self::assertEquals($drawing->getWidth(), 70);
        self::assertEquals($drawing->getHeight(), 70);
        $comment = $sheet->getComment('A4');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');

        $comment = $sheet->getComment('A4');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_BMP);

        // Add bmp image to comment background
        $sheet->setCellValue('A5', '.bmp 256 colors');
        $drawing = new Drawing();
        $drawing->setName('Brown Square');
        $drawing->setPath('tests/data/Writer/XLSX/brown_square_256.bmp');
        self::assertEquals($drawing->getWidth(), 70);
        self::assertEquals($drawing->getHeight(), 70);
        $comment = $sheet->getComment('A5');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');

        $comment = $sheet->getComment('A5');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_BMP);

        // Add bmp image to comment background
        $sheet->setCellValue('A6', '.bmp 24 bit');
        $drawing = new Drawing();
        $drawing->setName('Orange Square');
        $drawing->setPath('tests/data/Writer/XLSX/orange_square_24_bit.bmp');
        self::assertEquals($drawing->getWidth(), 70);
        self::assertEquals($drawing->getHeight(), 70);
        $comment = $sheet->getComment('A6');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');

        $comment = $sheet->getComment('A6');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_BMP);

        // Add unsupported tiff image to comment background
        $sheet->setCellValue('A7', '.tiff');
        $drawing = new Drawing();
        $drawing->setName('Purple Square');
        $drawing->setPath('tests/data/Writer/XLSX/purple_square.tiff');
        $comment = $sheet->getComment('A7');
        self::assertTrue($comment instanceof Comment);
        self::assertFalse($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_UNKNOWN);

        try {
            $comment->setBackgroundImage($drawing);
            self::fail('Should throw exception when attempting to add tiff');
        } catch (PhpSpreadsheetException $e) {
            self::assertTrue($e instanceof PhpSpreadsheetException);
            self::assertEquals($e->getMessage(), 'Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        try {
            $drawing->getImageTypeForSave();
            self::fail('Should throw exception when attempting to get image type for tiff');
        } catch (PhpSpreadsheetException $e) {
            self::assertTrue($e instanceof PhpSpreadsheetException);
            self::assertEquals($e->getMessage(), 'Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        try {
            $drawing->getImageFileExtensionForSave();
            self::fail('Should throw exception when attempting to get image file extention for tiff');
        } catch (PhpSpreadsheetException $e) {
            self::assertTrue($e instanceof PhpSpreadsheetException);
            self::assertEquals($e->getMessage(), 'Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        try {
            $drawing->getImageMimeType();
            self::fail('Should throw exception when attempting to get image mime type for tiff');
        } catch (PhpSpreadsheetException $e) {
            self::assertTrue($e instanceof PhpSpreadsheetException);
            self::assertEquals($e->getMessage(), 'Unsupported image type in comment background. Supported types: PNG, JPEG, BMP, GIF.');
        }

        // Write file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFileName = File::sysGetTempDir() . '/drawings_in_comments.xlsx';
        $writer->save($tempFileName);

        // Read new file
        $reloadedSpreadsheet = $reader->load($tempFileName);
        $sheet = $reloadedSpreadsheet->getActiveSheet();

        // Check first image in comment background
        $comment = $sheet->getComment('A1');
        self::assertEquals($comment->getWidth(), '75pt');
        self::assertEquals($comment->getHeight(), '75pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 100);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 100);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check second image in comment background
        $comment = $sheet->getComment('A2');
        self::assertEquals($comment->getWidth(), '112.5pt');
        self::assertEquals($comment->getHeight(), '112.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 150);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 150);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check third image in comment background
        $comment = $sheet->getComment('A3');
        self::assertEquals($comment->getWidth(), '37.5pt');
        self::assertEquals($comment->getHeight(), '37.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 50);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 50);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_JPEG);

        // Check fourth image in comment background
        $comment = $sheet->getComment('A4');
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 70);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 70);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check fifth image in comment background
        $comment = $sheet->getComment('A5');
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 70);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 70);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check sixth image in comment background
        $comment = $sheet->getComment('A6');
        self::assertEquals($comment->getWidth(), '52.5pt');
        self::assertEquals($comment->getHeight(), '52.5pt');
        self::assertTrue($comment instanceof Comment);
        self::assertTrue($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 70);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 70);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_PNG);

        // Check seventh image in comment background
        $comment = $sheet->getComment('A7');
        self::assertTrue($comment instanceof Comment);
        self::assertFalse($comment->hasBackgroundImage());
        self::assertTrue($comment->getBackgroundImage() instanceof Drawing);
        self::assertEquals($comment->getBackgroundImage()->getWidth(), 0);
        self::assertEquals($comment->getBackgroundImage()->getHeight(), 0);
        self::assertEquals($comment->getBackgroundImage()->getType(), IMAGETYPE_UNKNOWN);

        unlink($tempFileName);

        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test save and load XLSX file with drawing image that coordinate is two cell anchor.
     */
    public function testTwoCellAnchorDrawing(): void
    {
        $reader = new Xlsx();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add gif image that coordinates is two cell anchor.
        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setPath('tests/data/Writer/XLSX/green_square.gif');
        self::assertEquals($drawing->getWidth(), 150);
        self::assertEquals($drawing->getHeight(), 150);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(30);
        $drawing->setOffsetY(10);
        $drawing->setCoordinates2('E8');
        $drawing->setOffsetX2(-50);
        $drawing->setOffsetY2(-20);
        $drawing->setWorksheet($sheet);

        // Write file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFileName = File::sysGetTempDir() . '/drawings_image_that_two_cell_anchor.xlsx';
        $writer->save($tempFileName);

        // Read new file
        $reloadedSpreadsheet = $reader->load($tempFileName);
        $sheet = $reloadedSpreadsheet->getActiveSheet();

        // Check image coordinates.
        $drawingCollection = $sheet->getDrawingCollection();
        $drawing = $drawingCollection[0];
        self::assertNotNull($drawing);

        self::assertEquals($drawing->getWidth(), 150);
        self::assertEquals($drawing->getHeight(), 150);
        self::assertEquals($drawing->getCoordinates(), 'A1');
        self::assertEquals($drawing->getOffsetX(), 30);
        self::assertEquals($drawing->getOffsetY(), 10);
        self::assertEquals($drawing->getCoordinates2(), 'E8');
        self::assertEquals($drawing->getOffsetX2(), -50);
        self::assertEquals($drawing->getOffsetY2(), -20);
        self::assertEquals($drawing->getWorksheet(), $sheet);

        unlink($tempFileName);

        self::assertNotNull($reloadedSpreadsheet);
    }
}

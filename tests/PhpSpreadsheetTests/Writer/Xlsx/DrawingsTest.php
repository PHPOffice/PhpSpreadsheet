<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

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

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFileName);

        $reloadedSpreadsheet = $reader->load($tempFileName);
        unlink($tempFileName);

        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($reloadedSpreadsheet);
    }

    /**
     * Test build and save XLSX with drawings in comments with comment size correction
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
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
        $comment = $sheet->getComment('A1');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Add gif image to comment background
        $sheet->setCellValue('A2', '.gif');
        $drawing = new Drawing();
        $drawing->setName('Green Square');
        $drawing->setPath('tests/data/Writer/XLSX/green_square.gif');
        $comment = $sheet->getComment('A2');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Add jpeg image to comment background
        $sheet->setCellValue('A3', '.jpeg');
        $drawing = new Drawing();
        $drawing->setName('Red Square');
        $drawing->setPath('tests/data/Writer/XLSX/red_square.jpeg');
        $comment = $sheet->getComment('A3');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Add bmp image to comment background
        $sheet->setCellValue('A4', '.bmp 16 colors');
        $drawing = new Drawing();
        $drawing->setName('Yellow Square');
        $drawing->setPath('tests/data/Writer/XLSX/yellow_square_16.bmp');
        $comment = $sheet->getComment('A4');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Add bmp image to comment background
        $sheet->setCellValue('A5', '.bmp 256 colors');
        $drawing = new Drawing();
        $drawing->setName('Brown Square');
        $drawing->setPath('tests/data/Writer/XLSX/brown_square_256.bmp');
        $comment = $sheet->getComment('A5');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Add bmp image to comment background
        $sheet->setCellValue('A6', '.bmp 24 bit');
        $drawing = new Drawing();
        $drawing->setName('Orange Square');
        $drawing->setPath('tests/data/Writer/XLSX/orange_square_24_bit.bmp');
        $comment = $sheet->getComment('A6');
        $comment->setBackgroundImage($drawing);
        $comment->setSizeAsBackgroundImage();

        // Write file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $tempFileName = File::sysGetTempDir() . '/drawings_in_comments.xlsx';
        $writer->save($tempFileName);

        // Read new file
        $reloadedSpreadsheet = $reader->load($tempFileName);
        unlink($tempFileName);

        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($reloadedSpreadsheet);
    }
}

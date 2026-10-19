<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class Issue1790Test extends TestCase
{
    public function testCommentStylingOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $comment = $sheet->getComment('A1');

        $comment->getFillColor()->setRGB('FF5733');
        $comment->setBorderColor(new Color('0000FF'));
        $comment->setFillOpacity(0.6);
        $comment->setShapeType(203);

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tempFile === false) {
            self::fail('Could not create temporary file');
        }
        $writer->save($tempFile);

        $zip = new ZipArchive();
        $zip->open($tempFile);
        $vml = $zip->getFromName('xl/drawings/vmlDrawing1.vml');
        $zip->close();
        unlink($tempFile);

        self::assertIsString($vml);

        self::assertStringContainsString('fillcolor="#FF5733"', $vml);
        self::assertStringContainsString('strokecolor="#0000FF"', $vml);
        self::assertStringContainsString('opacity="0.6"', $vml);
        self::assertStringContainsString('type="#_x0000_t203"', $vml);
    }

    public function testCommentStylingRoundTrip(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $comment = $sheet->getComment('A1');
        $comment->getFillColor()->setRGB('FF5733');
        $comment->setBorderColor(new Color('0000FF'));
        $comment->setFillOpacity(0.6);
        $comment->setShapeType(203);
        $comment->setAuthor('TestAuthor');
        $comment->getText()->createText('Test comment text');

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tempFile === false) {
            self::fail('Could not create temporary file');
        }
        $writer->save($tempFile);

        $reader = new XlsxReader();
        $loadedSpreadsheet = $reader->load($tempFile);
        $loadedComment = $loadedSpreadsheet->getActiveSheet()->getComment('A1');

        self::assertEquals('FF5733', $loadedComment->getFillColor()->getRGB());
        self::assertEquals('0000FF', $loadedComment->getBorderColor()->getRGB());
        self::assertEquals(0.6, $loadedComment->getFillOpacity());
        self::assertEquals(203, $loadedComment->getShapeType());
        self::assertEquals('TestAuthor', $loadedComment->getAuthor());
        self::assertEquals('Test comment text', $loadedComment->getText()->getPlainText());

        unlink($tempFile);
        $spreadsheet->disconnectWorksheets();
        $loadedSpreadsheet->disconnectWorksheets();
    }

    public function testMultipleCommentStyling(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $comment1 = $sheet->getComment('A1');
        $comment1->getFillColor()->setRGB('FF0000');
        $comment1->setBorderColor(new Color('00FF00'));
        $comment1->setFillOpacity(0.3);
        $comment1->setShapeType(202);
        $comment1->getText()->createText('Red comment');

        $comment2 = $sheet->getComment('B2');
        $comment2->getFillColor()->setRGB('0000FF');
        $comment2->setBorderColor(new Color('FFFF00'));
        $comment2->setFillOpacity(0.8);
        $comment2->setShapeType(204);
        $comment2->getText()->createText('Blue comment');

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tempFile === false) {
            self::fail('Could not create temporary file');
        }
        $writer->save($tempFile);

        $reader = new XlsxReader();
        $loadedSpreadsheet = $reader->load($tempFile);
        $loadedSheet = $loadedSpreadsheet->getActiveSheet();

        $loadedComment1 = $loadedSheet->getComment('A1');
        self::assertEquals('FF0000', $loadedComment1->getFillColor()->getRGB());
        self::assertEquals('00FF00', $loadedComment1->getBorderColor()->getRGB());
        self::assertEquals(0.3, $loadedComment1->getFillOpacity());
        self::assertEquals(202, $loadedComment1->getShapeType());

        $loadedComment2 = $loadedSheet->getComment('B2');
        self::assertEquals('0000FF', $loadedComment2->getFillColor()->getRGB());
        self::assertEquals('FFFF00', $loadedComment2->getBorderColor()->getRGB());
        self::assertEquals(0.8, $loadedComment2->getFillOpacity());
        self::assertEquals(204, $loadedComment2->getShapeType());

        unlink($tempFile);
        $spreadsheet->disconnectWorksheets();
        $loadedSpreadsheet->disconnectWorksheets();
    }
}

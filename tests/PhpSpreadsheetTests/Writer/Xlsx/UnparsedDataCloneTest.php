<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class UnparsedDataCloneTest extends TestCase
{
    /**
     * Test load and save Xlsx file with unparsed data (form elements, protected sheets, alternate contents, printer settings,..).
     */
    public function testLoadSaveXlsxWithUnparsedDataClone(): void
    {
        $sampleFilename = 'tests/data/Writer/XLSX/drawing_on_2nd_page.xlsx';
        $resultFilename = File::temporaryFilename();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($sampleFilename);
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        $sheetCodeName = $sheet->getCodeName();
        $unparsedLoadedData = $spreadsheet->getUnparsedLoadedData();
        self::assertArrayHasKey('printerSettings', $unparsedLoadedData['sheets'][$sheetCodeName]);
        self::assertCount(1, $unparsedLoadedData['sheets'][$sheetCodeName]['printerSettings']);

        $clonedSheet = clone $spreadsheet->getActiveSheet();
        $clonedSheet->setTitle('Clone');
        $spreadsheet->addSheet($clonedSheet);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($resultFilename);
        $dupname = 'Unable to open saved file';

        $zip = new ZipArchive();
        if ($zip->open($resultFilename) !== false) {
            $names = [];
            $dupname = '';
            for ($index = 0; $index < $zip->numFiles; ++$index) {
                $filename = $zip->getNameIndex($index);
                if (in_array($filename, $names)) {
                    $dupname .= "$filename,";
                } else {
                    $names[] = $filename;
                }
            }
            $zip->close();
        }
        unlink($resultFilename);
        self::assertEquals('', $dupname);
    }

    /**
     * Test that saving twice with same writer works.
     */
    public function testSaveTwice(): void
    {
        $sampleFilename = 'tests/data/Writer/XLSX/drawing_on_2nd_page.xlsx';
        $resultFilename1 = File::temporaryFilename();
        $resultFilename2 = File::temporaryFilename();
        self::assertNotEquals($resultFilename1, $resultFilename2);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($sampleFilename);
        $sheet = $spreadsheet->setActiveSheetIndex(1);
        $sheet->setTitle('Original');

        $clonedSheet = clone $spreadsheet->getActiveSheet();
        $clonedSheet->setTitle('Clone');
        $spreadsheet->addSheet($clonedSheet);
        $clonedSheet->getCell('A8')->setValue('cloned');
        $sheet->getCell('A8')->setValue('original');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($resultFilename1);
        $reader1 = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet1 = $reader1->load($resultFilename1);
        unlink($resultFilename1);
        $sheet1c = $spreadsheet1->getSheetByNameOrThrow('Clone');
        $sheet1o = $spreadsheet1->getSheetByNameOrThrow('Original');

        $writer->save($resultFilename2);
        $reader2 = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet2 = $reader2->load($resultFilename2);
        unlink($resultFilename2);
        $sheet2c = $spreadsheet2->getSheetByNameOrThrow('Clone');
        $sheet2o = $spreadsheet2->getSheetByNameOrThrow('Original');

        self::assertEquals($spreadsheet1->getSheetCount(), $spreadsheet2->getSheetCount());
        self::assertCount(1, $sheet1c->getDrawingCollection());
        self::assertCount(1, $sheet1o->getDrawingCollection());
        self::assertCount(1, $sheet2c->getDrawingCollection());
        self::assertCount(1, $sheet2o->getDrawingCollection());
        self::assertEquals('original', $sheet1o->getCell('A8')->getValue());
        self::assertEquals('original', $sheet2o->getCell('A8')->getValue());
        self::assertEquals('cloned', $sheet1c->getCell('A8')->getValue());
        self::assertEquals('cloned', $sheet2c->getCell('A8')->getValue());
    }
}

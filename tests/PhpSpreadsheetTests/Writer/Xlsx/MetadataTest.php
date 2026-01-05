<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    private function getMetadata(): string
    {
        $zipfile = "zip://{$this->outputFile}#xl/metadata.xml";

        return (string) @file_get_contents($zipfile);
    }

    public function testNoArrayNoPixInCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World!');

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $contents = $this->getMetadata();
        self::assertSame('', $contents);
    }

    public function testArrayNoPixInCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->returnArrayAsArray();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $sheet->setCellValue('A2', 5);
        $sheet->setCellValue('A3', -1);
        $sheet->setCellValue('A4', 15);
        $sheet->setCellValue('A5', 20);
        $sheet->setCellValue('C1', '=SORT(A1:A5)');

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $contents = $this->getMetadata();
        $expectedArray = [
            'xmlns:xda',
            '<metadataTypes count="2">',
            '<metadataType name="XLDAPR"',
            '<metadataType name="XLRICHVALUE"',
            '<futureMetadata name="XLDAPR" count="1">',
            '<futureMetadata name="XLRICHVALUE" count="1">',
            '<cellMetadata count="1">',
            '<valueMetadata count="1">',
        ];
        foreach ($expectedArray as $expected) {
            self::assertStringContainsString($expected, $contents);
        }
    }

    public function testArrayPixInCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->returnArrayAsArray();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1],
            [9],
            [-1],
            [15],
        ]);
        $sheet->setCellValue('H1', '=SORT(A1:A4)');
        $objDrawing = new Drawing();
        $directory = 'tests/data/Writer/XLSX';
        $objDrawing->setPath($directory . '/blue_square.png');
        $sheet->getCell('C2')->setValue($objDrawing);
        $objDrawing2 = new Drawing();
        $objDrawing2->setPath($directory . '/red_square.jpeg');
        $sheet->getCell('C5')->setValue($objDrawing2);

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $contents = $this->getMetadata();
        //echo $contents;
        $expectedArray = [
            'xmlns:xda',
            '<metadataTypes count="2">',
            '<metadataType name="XLDAPR"',
            '<metadataType name="XLRICHVALUE"',
            '<futureMetadata name="XLDAPR" count="1">',
            '<futureMetadata name="XLRICHVALUE" count="2">',
            '<cellMetadata count="1">',
            '<valueMetadata count="2">',
        ];
        foreach ($expectedArray as $expected) {
            self::assertStringContainsString($expected, $contents);
        }
        //self::assertTrue(true);
    }

    public function testNoArrayPixInCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $objDrawing = new Drawing();
        $directory = 'tests/data/Writer/XLSX';
        $objDrawing->setPath($directory . '/blue_square.png');
        $sheet->getCell('C2')->setValue($objDrawing);
        $objDrawing2 = new Drawing();
        $objDrawing2->setPath($directory . '/red_square.jpeg');
        $sheet->getCell('C5')->setValue($objDrawing2);

        $writer = new XlsxWriter($spreadsheet);
        $this->outputFile = File::temporaryFilename();
        $writer->save($this->outputFile);
        $contents = $this->getMetadata();
        $unexpectedArray = [
            'xmlns:xda',
            '<metadataType name="XLDAPR"',
            '<futureMetadata name="XLDAPR"',
            '<cellMetadata',
        ];
        $expectedArray = [
            '<metadataTypes count="1">',
            '<metadataType name="XLRICHVALUE"',
            '<futureMetadata name="XLRICHVALUE" count="2">',
            '<valueMetadata count="2">',
        ];
        foreach ($expectedArray as $expected) {
            self::assertStringContainsString($expected, $contents);
        }
        foreach ($unexpectedArray as $unexpected) {
            self::assertStringNotContainsString($unexpected, $contents);
        }
    }
}

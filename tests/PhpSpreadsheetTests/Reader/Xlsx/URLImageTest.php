<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheetTests\Reader\Utility\File;
use PHPUnit\Framework\TestCase;

class URLImageTest extends TestCase
{
    public function testDefault(): void
    {
        $reader = new XlsxReader();
        self::assertFalse($reader->getAllowExternalImages());
    }

    public function testURLImageSourceAllowed(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath('tests/data/Reader/XLSX/urlImage.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $reader->setAllowExternalImages(true);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(1, $collection);

        foreach ($collection as $drawing) {
            self::assertInstanceOf(Drawing::class, $drawing);
            // Check if the source is a URL or a file path
            self::assertTrue($drawing->getIsURL());
            self::assertSame(
                'https://phpspreadsheet.readthedocs.io/en/latest'
                . '/topics/images/01-03-filter-icon-1.png',
                $drawing->getPath()
            );
            self::assertSame(IMAGETYPE_PNG, $drawing->getType());
            self::assertSame(84, $drawing->getWidth());
            self::assertSame(44, $drawing->getHeight());
        }
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceAllowedFlag(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath('tests/data/Reader/XLSX/urlImage.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename, XlsxReader::ALLOW_EXTERNAL_IMAGES);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(1, $collection);

        foreach ($collection as $drawing) {
            self::assertInstanceOf(Drawing::class, $drawing);
            // Check if the source is a URL or a file path
            self::assertTrue($drawing->getIsURL());
            self::assertSame(
                'https://phpspreadsheet.readthedocs.io/en/latest'
                    . '/topics/images/01-03-filter-icon-1.png',
                $drawing->getPath()
            );
            self::assertSame(IMAGETYPE_PNG, $drawing->getType());
            self::assertSame(84, $drawing->getWidth());
            self::assertSame(44, $drawing->getHeight());
        }
        $spreadsheet->disconnectWorksheets();
    }

    private function suppliedWhiteList(string $path): bool
    {
        return str_ends_with($path, 'autofilter.png');
    }

    public function testExternalImagesWhitelist(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath('tests/data/Reader/XLSX/urlImage2.xlsx');
        self::assertNotFalse($filename);
        $reader = new XlsxReader();
        $reader->setAllowExternalImages(true)
            ->setIsWhitelisted($this->suppliedWhiteList(...));
        $spreadsheet = $reader->load($filename);
        $sheet1 = $spreadsheet->getSheetByNameOrThrow('Sheet1');
        $drawings1 = $sheet1->getDrawingCollection();
        self::assertCount(0, $drawings1);
        $sheet2 = $spreadsheet->getSheetByNameOrThrow('Sheet2');
        $drawings2 = $sheet2->getDrawingCollection();
        self::assertCount(1, $drawings2);
        $drawing = $drawings2[0];
        self::assertInstanceOf(Drawing::class, $drawing);
        self::assertSame(
            'https://phpspreadsheet.readthedocs.io/en/latest'
                . '/topics/images/01-02-autofilter.png',
            $drawing->getPath()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testExternalImagesNoWhitelist(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath('tests/data/Reader/XLSX/urlImage2.xlsx');
        self::assertNotFalse($filename);
        $reader = new XlsxReader();
        $reader->setAllowExternalImages(true);
        $spreadsheet = $reader->load($filename);
        $sheet1 = $spreadsheet->getSheetByNameOrThrow('Sheet1');
        $drawings1 = $sheet1->getDrawingCollection();
        self::assertCount(1, $drawings1);
        $drawing1 = $drawings1[0];
        self::assertInstanceOf(Drawing::class, $drawing1);
        self::assertSame(
            'https://phpspreadsheet.readthedocs.io/en/latest'
                . '/topics/images/01-03-filter-icon-1.png',
            $drawing1->getPath()
        );
        $sheet2 = $spreadsheet->getSheetByNameOrThrow('Sheet2');
        $drawings2 = $sheet2->getDrawingCollection();
        self::assertCount(2, $drawings2);
        $drawing2a = $drawings2[0];
        self::assertInstanceOf(Drawing::class, $drawing2a);
        self::assertSame(
            'https://phpspreadsheet.readthedocs.io/en/latest'
                . '/topics/images/01-02-autofilter.png',
            $drawing2a->getPath()
        );
        $drawing2b = $drawings2[1];
        self::assertInstanceOf(Drawing::class, $drawing2b);
        self::assertSame(
            'https://phpspreadsheet.readthedocs.io/en/latest'
                . '/topics/images/01-03-filter-icon-1.png',
            $drawing2b->getPath()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceNotAllowed(): void
    {
        $filename = realpath('tests/data/Reader/XLSX/urlImage.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $reader->setAllowExternalImages(false);
        self::assertFalse($reader->getAllowExternalImages());
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(0, $collection);
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceNotFoundAllowed(): void
    {
        if (getenv('SKIP_URL_IMAGE_TEST') === '1') {
            self::markTestSkipped('Skipped due to setting of environment variable');
        }
        $filename = realpath('tests/data/Reader/XLSX/urlImage.notfound.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $reader->setAllowExternalImages(true);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(0, $collection);
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceNotFoundNotAllowed(): void
    {
        $filename = realpath('tests/data/Reader/XLSX/urlImage.notfound.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $reader->setAllowExternalImages(false);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(0, $collection);
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceNotFoundNotAllowedFlag(): void
    {
        $filename = realpath('tests/data/Reader/XLSX/urlImage.notfound.xlsx');
        self::assertNotFalse($filename);
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename, XlsxReader::DONT_ALLOW_EXTERNAL_IMAGES);
        $worksheet = $spreadsheet->getActiveSheet();
        $collection = $worksheet->getDrawingCollection();
        self::assertCount(0, $collection);
        $spreadsheet->disconnectWorksheets();
    }

    public function testURLImageSourceBadProtocol(): void
    {
        $filename = realpath('tests/data/Reader/XLSX/urlImage.bad.dontuse');
        self::assertNotFalse($filename);
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Invalid protocol for linked drawing');
        $reader = IOFactory::createReader('Xlsx');
        $reader->load($filename);
    }
}

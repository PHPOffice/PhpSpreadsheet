<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheetTests\Functional;

class ImagesRootTest extends Functional\AbstractFunctional
{
    private string $curdir = '';

    protected function setUp(): void
    {
        $curdir = getcwd();
        if ($curdir === false) {
            self::fail('Unable to obtain current directory');
        } else {
            $this->curdir = $curdir;
        }
    }

    protected function tearDown(): void
    {
        chdir($this->curdir);
    }

    public function testImagesRoot(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Test');
        $drawing->setDescription('Test');
        $root = 'http://www.example.com';
        $newdir = __DIR__ . '/../../../data/Reader/HTML';
        $stub = 'image.jpg';
        $imagePath = "./$stub";
        chdir($newdir);
        self::assertFileExists($imagePath);
        $drawing->setPath($imagePath);
        $desc = 'Test <img> tag';
        $drawing->setDescription($desc);
        $drawing->setHeight(36);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $drawing->setCoordinates('A1');
        $sheet->setCellValue('A2', 'Image Above?');

        $writer = new Html($spreadsheet);
        $writer->setImagesRoot($root);
        $html = $writer->generateHTMLAll();
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body')->item(0);
        self::assertNotNull($body);
        $divs = $body->getElementsByTagName('div');

        $tabl = $divs->item(0)?->getElementsByTagName('table');
        $tbod = $tabl?->item(0)?->getElementsByTagName('tbody');
        $rows = $tbod?->item(0)?->getElementsByTagName('tr');
        self::assertCount(2, $rows);

        $tds = $rows?->item(0)?->getElementsByTagName('td');
        self::assertCount(1, $tds);
        $img = $tds?->item(0)?->getElementsByTagName('img');
        self::assertCount(1, $img);
        self::assertSame("$root/$stub", $img?->item(0)?->getAttribute('src'));
        self::assertSame($desc, $img->item(0)->getAttribute('alt'));
        $spreadsheet->disconnectWorksheets();
    }
}

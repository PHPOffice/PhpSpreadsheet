<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PHPUnit\Framework\TestCase;

class OpacityTest extends TestCase
{
    public function testOpacity(): void
    {
        $filename = 'tests/data/Reader/HTML/html.opacity.3.html';
        $reader = new Html();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $drawings = $sheet->getDrawingCollection();
        self::assertCount(1, $drawings);
        self::assertNotNull($drawings[0]);
        self::assertSame(60000, $drawings[0]->getOpacity());
        self::assertSame(192, $drawings[0]->getHeight());
        self::assertSame(192, $drawings[0]->getWidth());
        $spreadsheet->disconnectWorksheets();
    }
}

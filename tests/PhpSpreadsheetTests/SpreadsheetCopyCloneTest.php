<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SpreadsheetCopyCloneTest extends TestCase
{
    private ?Spreadsheet $spreadsheet = null;

    private ?Spreadsheet $spreadsheet2 = null;

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
        if ($this->spreadsheet2 !== null) {
            $this->spreadsheet2->disconnectWorksheets();
            $this->spreadsheet2 = null;
        }
    }

    public function runTests(string $type): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('original');
        $sheet->getStyle('A1')->getFont()->setName('font1');
        $sheet->getStyle('A2')->getFont()->setName('font2');
        $sheet->getStyle('A3')->getFont()->setName('font3');
        $sheet->getStyle('B1')->getFont()->setName('font1');
        $sheet->getStyle('B2')->getFont()->setName('font2');
        $sheet->getCell('A1')->setValue('this is a1');
        $sheet->getCell('A2')->setValue('this is a2');
        $sheet->getCell('A3')->setValue('this is a3');
        $sheet->getCell('B1')->setValue('this is b1');
        $sheet->getCell('B2')->setValue('this is b2');
        self::assertSame('font1', $sheet->getStyle('A1')->getFont()->getName());
        if ($type === 'copy') {
            $this->spreadsheet2 = $this->spreadsheet->copy();
        } else {
            $this->spreadsheet2 = clone $this->spreadsheet;
        }
        $copysheet = $this->spreadsheet2->getActiveSheet();
        self::assertSame('original', $copysheet->getTitle());
        $copysheet->setTitle('unoriginal');
        self::assertSame('original', $sheet->getTitle());
        self::assertSame('unoriginal', $copysheet->getTitle());
        $copysheet->getStyle('A2')->getFont()->setName('font12');
        $copysheet->getCell('A2')->setValue('this was a2');

        self::assertSame('font1', $sheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $sheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $sheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $sheet->getStyle('B2')->getFont()->getName());
        self::assertSame('this is a1', $sheet->getCell('A1')->getValue());
        self::assertSame('this is a2', $sheet->getCell('A2')->getValue());
        self::assertSame('this is a3', $sheet->getCell('A3')->getValue());
        self::assertSame('this is b1', $sheet->getCell('B1')->getValue());
        self::assertSame('this is b2', $sheet->getCell('B2')->getValue());

        self::assertSame('font1', $copysheet->getStyle('A1')->getFont()->getName());
        self::assertSame('font12', $copysheet->getStyle('A2')->getFont()->getName());
        self::assertSame('font3', $copysheet->getStyle('A3')->getFont()->getName());
        self::assertSame('font1', $copysheet->getStyle('B1')->getFont()->getName());
        self::assertSame('font2', $copysheet->getStyle('B2')->getFont()->getName());
        self::assertSame('this is a1', $copysheet->getCell('A1')->getValue());
        self::assertSame('this was a2', $copysheet->getCell('A2')->getValue());
        self::assertSame('this is a3', $copysheet->getCell('A3')->getValue());
        self::assertSame('this is b1', $copysheet->getCell('B1')->getValue());
        self::assertSame('this is b2', $copysheet->getCell('B2')->getValue());
    }

    public function testClone(): void
    {
        $this->runTests('clone');
    }

    public function testCopy(): void
    {
        $this->runTests('copy');
    }
}

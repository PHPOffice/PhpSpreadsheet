<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('providerCopyClone')]
    public function testCopyClone(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
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
        $sheet->setSelectedCells('A3');
        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }
        self::assertSame($spreadsheet, $spreadsheet->getCalculationEngine()->getSpreadsheet());
        self::assertSame($spreadsheet2, $spreadsheet2->getCalculationEngine()->getSpreadsheet());
        self::assertSame('A3', $sheet->getSelectedCells());
        $copysheet = $spreadsheet2->getActiveSheet();
        self::assertSame('A3', $copysheet->getSelectedCells());
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

    #[DataProvider('providerCopyClone')]
    public function testCopyCloneActiveSheet(string $type): void
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet0 = $this->spreadsheet->getActiveSheet();
        $sheet1 = $this->spreadsheet->createSheet();
        $sheet2 = $this->spreadsheet->createSheet();
        $sheet3 = $this->spreadsheet->createSheet();
        $sheet4 = $this->spreadsheet->createSheet();
        $sheet0->getStyle('B1')->getFont()->setName('whatever');
        $sheet1->getStyle('B1')->getFont()->setName('whatever');
        $sheet2->getStyle('B1')->getFont()->setName('whatever');
        $sheet3->getStyle('B1')->getFont()->setName('whatever');
        $sheet4->getStyle('B1')->getFont()->setName('whatever');
        $this->spreadsheet->setActiveSheetIndex(2);
        if ($type === 'copy') {
            $this->spreadsheet2 = $this->spreadsheet->copy();
        } else {
            $this->spreadsheet2 = clone $this->spreadsheet;
        }
        self::assertSame(2, $this->spreadsheet2->getActiveSheetIndex());
    }

    public static function providerCopyClone(): array
    {
        return [
            ['copy'],
            ['clone'],
        ];
    }

    public static function providerCopyClone2(): array
    {
        return [
            ['copy', true, false, true, 'array'],
            ['clone', true, false, true, 'array'],
            ['copy', false, true, false, 'value'],
            ['clone', false, true, false, 'value'],
            ['copy', false, true, true, 'error'],
            ['clone', false, true, true, 'error'],
        ];
    }

    #[DataProvider('providerCopyClone2')]
    public function testCopyClone2(string $type, bool $suppress, bool $cache, bool $pruning, string $return): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $calc = $spreadsheet->getCalculationEngine();
        $calc->setSuppressFormulaErrors($suppress);
        $calc->setCalculationCacheEnabled($cache);
        $calc->setBranchPruningEnabled($pruning);
        $calc->setInstanceArrayReturnType($return);
        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }
        $calc2 = $spreadsheet2->getCalculationEngine();
        self::assertSame($suppress, $calc2->getSuppressFormulaErrors());
        self::assertSame($cache, $calc2->getCalculationCacheEnabled());
        self::assertSame($pruning, $calc2->getBranchPruningEnabled());
        self::assertSame($return, $calc2->getInstanceArrayReturnType());
    }
}

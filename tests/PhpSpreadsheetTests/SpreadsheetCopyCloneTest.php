<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
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

    #[DataProvider('providerCopyClone')]
    public function testCopyCloneDefinedName(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet2 = $spreadsheet->createSheet();
        $sheet->setTitle('original1');
        $sheet2->setTitle('original2');
        $spreadsheet->addDefinedName(
            DefinedName::createInstance('defname1', $sheet, '$A$1:$A$3')
        );
        $spreadsheet->addDefinedName(
            DefinedName::createInstance('defname2', $sheet2, '$B$1:$B$3', true, $sheet)
        );
        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }
        $copySheet = $spreadsheet2->getSheetByNameOrThrow('original1');
        $copySheet2 = $spreadsheet2->getSheetByNameOrThrow('original2');
        $definedName = $spreadsheet2->getDefinedName('defname1');
        self::assertNotNull($definedName);
        self::assertSame('$A$1:$A$3', $definedName->getValue());
        self::assertSame($copySheet, $definedName->getWorksheet());
        self::assertNull($definedName->getScope());
        $definedName = $spreadsheet2->getDefinedName('defname2', $copySheet);
        self::assertNotNull($definedName);
        self::assertSame('$B$1:$B$3', $definedName->getValue());
        self::assertSame($copySheet2, $definedName->getWorksheet());
        self::assertSame($copySheet, $definedName->getScope());
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

    #[DataProvider('providerCopyClone')]
    public function testCopyCloneImageInCell(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('original1');
        $objDrawing = new Drawing();
        $directory = 'tests/data/Writer/XLSX';
        $path = $directory . '/blue_square.png';
        $objDrawing->setPath($path);
        $sheet->getCell('C2')->setValue($objDrawing);

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }
        $copySheet = $spreadsheet2->getSheetByNameOrThrow('original1');
        $collection = $copySheet->getInCellDrawingCollection();
        self::assertCount(1, $collection);
        $image = $collection[0];
        self::assertInstanceOf(Drawing::class, $image);
        self::assertTrue($image->isInCell());
        self::assertSame($path, $image->getPath());
        self::assertNotSame($objDrawing, $image);
    }

    #[DataProvider('providerCopyClone')]
    public function testCopySheetCountMatches(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet()->setTitle('Sheet2');
        $spreadsheet->createSheet()->setTitle('Sheet3');

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }

        self::assertSame($spreadsheet->getSheetCount(), $spreadsheet2->getSheetCount());
        for ($i = 0; $i < $spreadsheet->getSheetCount(); ++$i) {
            self::assertSame(
                $spreadsheet->getSheet($i)->getTitle(),
                $spreadsheet2->getSheet($i)->getTitle()
            );
        }
    }

    #[DataProvider('providerCopyClone')]
    public function testCopyFormulasWork(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(10);
        $sheet->getCell('A2')->setValue(20);
        $sheet->getCell('A3')->setValue('=A1+A2');
        $sheet->getCell('B1')->setValue('=SUM(A1:A2)');

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }

        $copySheet = $spreadsheet2->getActiveSheet();
        // Verify formula strings are preserved
        self::assertSame('=A1+A2', $copySheet->getCell('A3')->getValue());
        self::assertSame('=SUM(A1:A2)', $copySheet->getCell('B1')->getValue());
        // Verify formulas calculate correctly in the copy
        self::assertSame(30, $copySheet->getCell('A3')->getCalculatedValue());
        self::assertSame(30, $copySheet->getCell('B1')->getCalculatedValue());

        // Modify source values and verify copy is independent
        $sheet->getCell('A1')->setValue(100);
        self::assertSame(10, $copySheet->getCell('A1')->getValue());
        self::assertSame(30, $copySheet->getCell('A3')->getCalculatedValue());
    }

    #[DataProvider('providerCopyClone')]
    public function testCopyNamedRangesReferenceNewSpreadsheet(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');
        $sheet->getCell('A1')->setValue(5);
        $sheet->getCell('A2')->setValue(15);
        $spreadsheet->addDefinedName(
            DefinedName::createInstance('MyRange', $sheet, '$A$1:$A$2')
        );

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }

        $copySheet = $spreadsheet2->getSheetByNameOrThrow('Data');
        $definedName = $spreadsheet2->getDefinedName('MyRange');
        self::assertNotNull($definedName);
        // The defined name worksheet must point to the copy, not the original
        self::assertSame($copySheet, $definedName->getWorksheet());
        self::assertNotSame($sheet, $definedName->getWorksheet());
    }

    #[DataProvider('providerCopyClone')]
    public function testCopyCanBeSavedWithoutErrors(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TestSheet');
        $sheet->getCell('A1')->setValue('Hello');
        $sheet->getCell('A2')->setValue(42);
        $sheet->getCell('A3')->setValue('=A2*2');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $spreadsheet->createSheet()->setTitle('Sheet2');
        $spreadsheet->getSheetByNameOrThrow('Sheet2')->getCell('B1')->setValue('World');

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }

        $filename = File::temporaryFilename();

        try {
            $writer = new XlsxWriter($spreadsheet2);
            $writer->save($filename);
            self::assertFileExists($filename);
            // Verify the saved file can be read back
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reloaded = $reader->load($filename);
            self::assertSame(2, $reloaded->getSheetCount());
            self::assertSame('Hello', $reloaded->getSheetByNameOrThrow('TestSheet')->getCell('A1')->getValue());
            self::assertSame('World', $reloaded->getSheetByNameOrThrow('Sheet2')->getCell('B1')->getValue());
            $reloaded->disconnectWorksheets();
        } finally {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    #[DataProvider('providerCopyClone')]
    public function testCopyWorksheetsAreIndependent(string $type): void
    {
        $spreadsheet = $this->spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('original');
        $sheet->getCell('B1')->setValue(100);

        if ($type === 'copy') {
            $spreadsheet2 = $this->spreadsheet2 = $spreadsheet->copy();
        } else {
            $spreadsheet2 = $this->spreadsheet2 = clone $spreadsheet;
        }

        $copySheet = $spreadsheet2->getActiveSheet();

        // Modify the copy
        $copySheet->getCell('A1')->setValue('modified');
        $copySheet->getCell('B1')->setValue(999);
        $copySheet->getCell('C1')->setValue('new cell');

        // Original must be unaffected
        self::assertSame('original', $sheet->getCell('A1')->getValue());
        self::assertSame(100, $sheet->getCell('B1')->getValue());
        self::assertNull($sheet->getCell('C1')->getValue());

        // Copy must have the new values
        self::assertSame('modified', $copySheet->getCell('A1')->getValue());
        self::assertSame(999, $copySheet->getCell('B1')->getValue());
        self::assertSame('new cell', $copySheet->getCell('C1')->getValue());

        // Worksheet parent references must point to their respective spreadsheets
        self::assertSame($spreadsheet, $sheet->getParent());
        self::assertSame($spreadsheet2, $copySheet->getParent());
    }
}

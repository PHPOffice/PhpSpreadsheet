<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class RetainSelectedCellsTest extends AbstractFunctional
{
    private string $fileName = '';

    protected function tearDown(): void
    {
        if ($this->fileName !== '') {
            unlink($this->fileName);
            $this->fileName = '';
        }
    }

    public static function providerFormats(): array
    {
        return [
            ['Xls'],
            ['Xlsx'],
            ['Ods'],
            ['Csv'],
            ['Html'],
        ];
    }

    /**
     * Test selected cell is retained in memory and in file written to disk.
     *
     * @dataProvider providerFormats
     */
    public function testRetainSelectedCells(string $format): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('A3');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('B1');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', '=SIN(1)')
            ->setCellValue('A2', '=SIN(2)')
            ->setCellValue('A3', '=SIN(3)')
            ->setCellValue('B1', '=SIN(4)')
            ->setCellValue('B2', '=SIN(5)')
            ->setCellValue('B3', '=SIN(6)')
            ->setCellValue('C1', '=SIN(7)')
            ->setCellValue('C2', '=SIN(8)')
            ->setCellValue('C3', '=SIN(9)');
        $sheet->setSelectedCell('C2');
        $spreadsheet->setActiveSheetIndex(1);

        $reloaded = $this->writeAndReload($spreadsheet, $format);
        self::assertEquals('A3', $spreadsheet->getSheet(0)->getSelectedCells());
        self::assertEquals('B1', $spreadsheet->getSheet(1)->getSelectedCells());
        self::assertEquals('C2', $spreadsheet->getSheet(2)->getSelectedCells());
        self::assertEquals(1, $spreadsheet->getActiveSheetIndex());
        // SelectedCells and ActiveSheet don't make sense for Html, Csv.
        if ($format === 'Xlsx' || $format === 'Xls' || $format === 'Ods') {
            self::assertEquals('A3', $reloaded->getSheet(0)->getSelectedCells());
            self::assertEquals('B1', $reloaded->getSheet(1)->getSelectedCells());
            self::assertEquals('C2', $reloaded->getSheet(2)->getSelectedCells());
            self::assertEquals(1, $reloaded->getActiveSheetIndex());
        }
    }

    /** @dataProvider providerFormats */
    public function testRetainAutoSize(string $type): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $array = [
            [1, 2, 3, 4, 5],
            [11, 12, 13, 14, 15],
            [21, 22, 23, 24, 25],
            [31, 32, 33, 34, 35],
        ];
        $sheet1->fromArray($array);
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->fromArray($array);
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->fromArray($array);
        $sheet1->getStyle('A1')->getFont()->setName('Arial');
        $sheet2->getStyle('A1')->getFont()->setName('Arial');
        $sheet3->getStyle('A1')->getFont()->setName('Arial');
        $sheet1->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet1->setSelectedCells('B2');
        $sheet2->setSelectedCells('C3');
        $sheet3->setSelectedCells('D4');
        $spreadsheet->setActiveSheetIndex(1);
        $activeCellSheet1 = $sheet1->getActiveCell();
        $activeCellSheet2 = $sheet2->getActiveCell();
        $activeCellSheet3 = $sheet3->getActiveCell();

        $this->fileName = File::temporaryFilename();
        $writer = IOFactory::createWriter($spreadsheet, $type);
        $writer->save($this->fileName);

        self::assertSame(1, $spreadsheet->getActiveSheetIndex());
        self::assertSame('B2', $spreadsheet->getSheet(0)->getSelectedCells());
        self::assertSame('C3', $spreadsheet->getSheet(1)->getSelectedCells());
        self::assertSame('D4', $spreadsheet->getSheet(2)->getSelectedCells());
        self::assertSame($activeCellSheet1, $spreadsheet->getSheet(0)->getActiveCell());
        self::assertSame($activeCellSheet2, $spreadsheet->getSheet(1)->getActiveCell());
        self::assertSame($activeCellSheet3, $spreadsheet->getSheet(2)->getActiveCell());

        $spreadsheet->disconnectWorksheets();
    }
}

<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CeilingFloorTest extends AbstractFunctional
{
    private const INFILE = 'tests/data/Reader/Ods/issue.407.ods';

    private const EXPECTED_ODS = [
        'A1' => '=FLOOR(2.5,1)',
        'A2' => '=FLOOR.MATH(2.5,1)',
        'A3' => '=FLOOR.PRECISE(2.5,1)',
        'A4' => '=FLOOR.ODS(2.5,1,0)',
        'C1' => '=CEILING(2.5,1)',
        'C2' => '=CEILING.MATH(2.5,1)',
        'C3' => '=CEILING.PRECISE(2.5,1)',
        'C4' => '=CEILING.ODS(2.5,1)',
        'E1' => '=FLOOR.ODS(37, -1, 0)',
        'F1' => '=CEILING.ODS(-37.2,1,0)',
    ];

    private const EXPECTED_XLSX = [
        'A1' => '=FLOOR(2.5,1)',
        'A2' => '=FLOOR.MATH(2.5,1)',
        'A3' => '=FLOOR.PRECISE(2.5,1)',
        'A4' => '=FLOOR.MATH(2.5,1,0)', // note different from ODS
        'C1' => '=CEILING(2.5,1)',
        'C2' => '=CEILING.MATH(2.5,1)',
        'C3' => '=CEILING.PRECISE(2.5,1)',
        'C4' => '=CEILING.MATH(2.5,1)', // different from ODS
        'E1' => '=FLOOR.MATH(37, -1, 0)', // different from ODS
        'F1' => '=CEILING.MATH(-37.2,1,0)', // different from ODS
    ];

    public function testReadAndWriteOds(): void
    {
        $reader = new OdsReader();
        $spreadsheetOld = $reader->load(self::INFILE);
        $oldSheet = $spreadsheetOld->getActiveSheet();
        foreach (self::EXPECTED_ODS as $key => $value) {
            self::assertSame(
                $value,
                $oldSheet->getCell($key)->getValue(),
                "Error in cell $key"
            );
        }
        self::assertSame('#NUM!', $oldSheet->getCell('E1')->getCalculatedValue());
        self::assertSame('#NUM!', $oldSheet->getCell('F1')->getCalculatedValue());

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Ods');
        $spreadsheetOld->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::EXPECTED_ODS as $key => $value) {
            self::assertSame(
                $value,
                $sheet->getCell($key)->getValue(),
                "Error in cell $key"
            );
        }
        self::assertSame('#NUM!', $sheet->getCell('E1')->getCalculatedValue());
        self::assertSame('#NUM!', $sheet->getCell('F1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReadAndWriteXlsx(): void
    {
        $reader = new OdsReader();
        $spreadsheetOld = $reader->load(self::INFILE);

        $spreadsheet = $this->writeAndReload($spreadsheetOld, 'Xlsx');
        $spreadsheetOld->disconnectWorksheets();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::EXPECTED_XLSX as $key => $value) {
            self::assertSame(
                $value,
                $sheet->getCell($key)->getValue(),
                "Error in cell $key"
            );
        }
        self::assertSame(37.0, $sheet->getCell('E1')->getCalculatedValue()); // different from ODS
        self::assertSame(-37.0, $sheet->getCell('F1')->getCalculatedValue()); // different from ODS

        $spreadsheet->disconnectWorksheets();
    }

    public function testWriteXcl(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=CEILING.XCL(17.8,8)');
        $sheet->getCell('B1')->setValue('=FLOOR.XCL(17.8,8)');

        $spreadsheetOds = $this->writeAndReload($spreadsheet, 'Ods');
        $sheetOds = $spreadsheetOds->getActiveSheet();
        self::assertSame('=CEILING(17.8,8)', $sheetOds->getCell('A1')->getValue());
        self::assertSame('=FLOOR(17.8,8)', $sheetOds->getCell('B1')->getValue());
        $spreadsheetOds->disconnectWorksheets();

        $spreadsheetXlsx = $this->writeAndReload($spreadsheet, 'Xlsx');
        $sheetXlsx = $spreadsheetXlsx->getActiveSheet();
        self::assertSame('=CEILING(17.8,8)', $sheetXlsx->getCell('A1')->getValue());
        self::assertSame('=FLOOR(17.8,8)', $sheetXlsx->getCell('B1')->getValue());
        $spreadsheetXlsx->disconnectWorksheets();

        $spreadsheetXls = $this->writeAndReload($spreadsheet, 'Xls');
        $sheetXls = $spreadsheetXls->getActiveSheet();
        self::assertSame('=CEILING(17.8,8)', $sheetXls->getCell('A1')->getValue());
        self::assertSame('=FLOOR(17.8,8)', $sheetXls->getCell('B1')->getValue());
        $spreadsheetXls->disconnectWorksheets();

        $spreadsheet->disconnectWorksheets();
    }
}

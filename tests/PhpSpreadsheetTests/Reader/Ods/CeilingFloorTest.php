<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Ods as OdsReader;
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
        self::assertSame('#VALUE!', $oldSheet->getCell('E1')->getCalculatedValue());
        self::assertSame('#VALUE!', $oldSheet->getCell('F1')->getCalculatedValue());

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
        self::assertSame('#VALUE!', $sheet->getCell('E1')->getCalculatedValue());
        self::assertSame('#VALUE!', $sheet->getCell('F1')->getCalculatedValue());

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
}

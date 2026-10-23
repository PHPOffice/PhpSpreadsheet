<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;
use PHPUnit\Framework\Attributes\DataProvider;

class ErrorValueTest extends AbstractFunctional
{
    #[DataProvider('providerErrorValues')]
    public function testErrorValue(string $error): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $error);
        self::assertSame(DataType::TYPE_ERROR, $sheet->getCell('A1')->getDataType());

        $data = (new OdsWriter\Content(new OdsWriter($spreadsheet)))->write();
        self::assertStringContainsString('table:formula="of:=' . $error . '"', $data);
        self::assertStringContainsString('<text:p>' . $error . '</text:p>', $data);

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods');
        $spreadsheet->disconnectWorksheets();
        self::assertSame($error, $reloaded->getActiveSheet()->getCell('A1')->getCalculatedValue());
        $reloaded->disconnectWorksheets();
    }

    public static function providerErrorValues(): array
    {
        return [
            '#NULL!' => [ExcelError::null()],
            '#DIV/0!' => [ExcelError::DIV0()],
            '#VALUE!' => [ExcelError::VALUE()],
            '#REF!' => [ExcelError::REF()],
            '#NAME?' => [ExcelError::NAME()],
            '#NUM!' => [ExcelError::NAN()],
            '#N/A' => [ExcelError::NA()],
            '#CALC!' => [ExcelError::CALC()],
        ];
    }
}

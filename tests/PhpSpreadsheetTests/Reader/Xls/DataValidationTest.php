<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PHPUnit\Framework\TestCase;

class DataValidationTest extends TestCase
{
    /**
     * @dataProvider dataValidationProvider
     */
    public function testDataValidation(string $expectedRange, array $expectedRule): void
    {
        $filename = 'tests/data/Reader/XLS/DataValidation.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $hasDataValidation = $sheet->dataValidationExists($expectedRange);
        self::assertTrue($hasDataValidation);

        $dataValidation = $sheet->getDataValidation($expectedRange);
        self::assertSame($expectedRule['type'], $dataValidation->getType());
        self::assertSame($expectedRule['operator'], $dataValidation->getOperator());
        self::assertSame($expectedRule['formula'], $dataValidation->getFormula1());
        $spreadsheet->disconnectWorksheets();
    }

    public static function dataValidationProvider(): array
    {
        return [
            [
                'B2',
                [
                    'type' => DataValidation::TYPE_WHOLE,
                    'operator' => DataValidation::OPERATOR_GREATERTHANOREQUAL,
                    'formula' => '18',
                ],
            ],
            [
                'B3',
                [
                    'type' => DataValidation::TYPE_LIST,
                    'operator' => DataValidation::OPERATOR_BETWEEN,
                    'formula' => '"Blocked,Pending,Approved"',
                ],
            ],
        ];
    }
}

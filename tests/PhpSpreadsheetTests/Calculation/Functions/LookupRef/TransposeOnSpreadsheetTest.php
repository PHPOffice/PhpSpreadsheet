<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\Attributes\DataProvider;

class TransposeOnSpreadsheetTest extends AllSetupTeardown
{
    #[DataProvider('providerTRANSPOSE')]
    public function testTRANSPOSE(mixed $expectedResult, mixed $matrix): void
    {
        $sheet = $this->getSheet();
        $this->setArrayAsArray();
        if (!is_array($matrix)) {
            $matrix = [$matrix];
        }
        $sheet->fromArray($matrix, null, 'A1', true);
        $highColumn = $sheet->getHighestDataColumn();
        $highRow = $sheet->getHighestDataRow();
        $newHighColumn = $highColumn;
        StringHelper::stringIncrement($newHighColumn);
        $sheet->getCell("{$newHighColumn}1")
            ->setValue("=TRANSPOSE(A1:$highColumn$highRow)");
        self::assertSame($expectedResult, $sheet->getCell("{$newHighColumn}1")->getCalculatedValue());
    }

    public static function providerTRANSPOSE(): array
    {
        return require 'tests/data/Calculation/LookupRef/TRANSPOSE.php';
    }
}

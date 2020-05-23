<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PHPUnit\Framework\TestCase;

class DataValidationTest extends TestCase
{
    public function testNoValidation(): void
    {
        $dataValidation = new DataValidation();
        self::assertSame('090624f04837265d79323c4a1b7e89d1', $dataValidation->getHashCode());
        $dataValidation->setType(DataValidation::TYPE_CUSTOM);

        self::assertSame('778f6c9e0ffcd5eaa7d8e1432d67f919', $dataValidation->getHashCode());
        self::assertSame('778f6c9e0ffcd5eaa7d8e1432d67f919', $dataValidation->getHashCode(), 'getHashCode() should not have side effect');
    }
}

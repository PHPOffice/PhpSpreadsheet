<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class TrueTest extends AllSetupTeardown
{
    public function testTRUE(): void
    {
        $this->runTestCase('TRUE', true);
    }
}

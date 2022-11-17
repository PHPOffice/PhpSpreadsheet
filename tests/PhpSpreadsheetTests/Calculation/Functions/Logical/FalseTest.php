<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Logical;

class FalseTest extends AllSetupTeardown
{
    public function testFALSE(): void
    {
        $this->runTestCase('FALSE', false);
    }
}

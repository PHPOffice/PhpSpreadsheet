<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PHPUnit\Framework\TestCase;

class ErrorReportingTest extends TestCase
{
    public function testErrorReporting(): void
    {
        $errorReporting = (string) ini_get('error_reporting');
        if ($errorReporting !== (string) E_ALL) {
            self::markTestIncomplete("error_reporting is $errorReporting");
        }
        self::assertTrue(true);
    }
}

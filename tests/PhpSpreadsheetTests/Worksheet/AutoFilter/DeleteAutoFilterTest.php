<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

class DeleteAutoFilterTest extends SetupTeardown
{
    public function testDelete(): void
    {
        // Issue 2281 - deprecation in PHP81 when deleting filter
        $sheet = $this->getSheet();
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange('H2:O256');
        $sheet->removeAutoFilter();
        self::assertSame('', $sheet->getAutoFilter()->getRange());
    }
}

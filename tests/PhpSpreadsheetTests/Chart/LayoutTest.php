<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testSetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();

        $result = $testInstance->setLayoutTarget($LayoutTargetValue);
        self::assertInstanceOf(Layout::class, $result);
    }

    public function testGetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();
        $testInstance->setLayoutTarget($LayoutTargetValue);

        $result = $testInstance->getLayoutTarget();
        self::assertEquals($LayoutTargetValue, $result);
    }
}

<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PHPUnit_Framework_TestCase;

class LayoutTest extends PHPUnit_Framework_TestCase
{
    public function testSetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();

        $result = $testInstance->setLayoutTarget($LayoutTargetValue);
        self::assertTrue($result instanceof Layout);
    }

    public function testGetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();
        $setValue = $testInstance->setLayoutTarget($LayoutTargetValue);

        $result = $testInstance->getLayoutTarget();
        self::assertEquals($LayoutTargetValue, $result);
    }
}

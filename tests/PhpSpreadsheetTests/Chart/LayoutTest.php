<?php

namespace PhpSpreadsheetTests\Chart;

use PhpSpreadsheet\Chart\Layout;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    public function testSetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();

        $result = $testInstance->setLayoutTarget($LayoutTargetValue);
        $this->assertTrue($result instanceof Layout);
    }

    public function testGetLayoutTarget()
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();
        $setValue = $testInstance->setLayoutTarget($LayoutTargetValue);

        $result = $testInstance->getLayoutTarget();
        $this->assertEquals($LayoutTargetValue, $result);
    }
}

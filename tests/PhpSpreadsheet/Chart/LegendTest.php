<?php

namespace PhpSpreadsheet\Tests\Chart;

use PhpSpreadsheet\Chart\Legend;

class LegendTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPosition()
    {
        $positionValues = array(
            Legend::POSITION_RIGHT,
            Legend::POSITION_LEFT,
            Legend::POSITION_TOP,
            Legend::POSITION_BOTTOM,
            Legend::POSITION_TOPRIGHT,
        );

        $testInstance = new Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPosition($positionValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidPositionReturnsFalse()
    {
        $testInstance = new Legend;

        $result = $testInstance->setPosition('BottomLeft');
        $this->assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPosition();
        $this->assertEquals(Legend::POSITION_RIGHT, $result);
    }

    public function testGetPosition()
    {
        $PositionValue = Legend::POSITION_BOTTOM;

        $testInstance = new Legend;
        $setValue = $testInstance->setPosition($PositionValue);

        $result = $testInstance->getPosition();
        $this->assertEquals($PositionValue, $result);
    }

    public function testSetPositionXL()
    {
        $positionValues = array(
            Legend::XL_LEGEND_POSITION_BOTTOM,
            Legend::XL_LEGEND_POSITION_CORNER,
            Legend::XL_LEGEND_POSITION_CUSTOM,
            Legend::XL_LEGEND_POSITION_LEFT,
            Legend::XL_LEGEND_POSITION_RIGHT,
            Legend::XL_LEGEND_POSITION_TOP,
        );

        $testInstance = new Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPositionXL($positionValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidXLPositionReturnsFalse()
    {
        $testInstance = new Legend;

        $result = $testInstance->setPositionXL(999);
        $this->assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPositionXL();
        $this->assertEquals(Legend::XL_LEGEND_POSITION_RIGHT, $result);
    }

    public function testGetPositionXL()
    {
        $PositionValue = Legend::XL_LEGEND_POSITION_CORNER;

        $testInstance = new Legend;
        $setValue = $testInstance->setPositionXL($PositionValue);

        $result = $testInstance->getPositionXL();
        $this->assertEquals($PositionValue, $result);
    }

    public function testSetOverlay()
    {
        $overlayValues = array(
            true,
            false,
        );

        $testInstance = new Legend;

        foreach ($overlayValues as $overlayValue) {
            $result = $testInstance->setOverlay($overlayValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidOverlayReturnsFalse()
    {
        $testInstance = new Legend;

        $result = $testInstance->setOverlay('INVALID');
        $this->assertFalse($result);

        $result = $testInstance->getOverlay();
        $this->assertFalse($result);
    }

    public function testGetOverlay()
    {
        $OverlayValue = true;

        $testInstance = new Legend;
        $setValue = $testInstance->setOverlay($OverlayValue);

        $result = $testInstance->getOverlay();
        $this->assertEquals($OverlayValue, $result);
    }
}

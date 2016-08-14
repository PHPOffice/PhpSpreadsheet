<?php

namespace PhpSpreadsheet\Tests\Chart;

class LegendTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPosition()
    {
        $positionValues = array(
            \PHPExcel\Chart\Legend::POSITION_RIGHT,
            \PHPExcel\Chart\Legend::POSITION_LEFT,
            \PHPExcel\Chart\Legend::POSITION_TOP,
            \PHPExcel\Chart\Legend::POSITION_BOTTOM,
            \PHPExcel\Chart\Legend::POSITION_TOPRIGHT,
        );

        $testInstance = new \PHPExcel\Chart\Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPosition($positionValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidPositionReturnsFalse()
    {
        $testInstance = new \PHPExcel\Chart\Legend;

        $result = $testInstance->setPosition('BottomLeft');
        $this->assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPosition();
        $this->assertEquals(\PHPExcel\Chart\Legend::POSITION_RIGHT, $result);
    }

    public function testGetPosition()
    {
        $PositionValue = \PHPExcel\Chart\Legend::POSITION_BOTTOM;

        $testInstance = new \PHPExcel\Chart\Legend;
        $setValue = $testInstance->setPosition($PositionValue);

        $result = $testInstance->getPosition();
        $this->assertEquals($PositionValue, $result);
    }

    public function testSetPositionXL()
    {
        $positionValues = array(
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_BOTTOM,
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_CORNER,
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_CUSTOM,
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_LEFT,
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_RIGHT,
            \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_TOP,
        );

        $testInstance = new \PHPExcel\Chart\Legend;

        foreach ($positionValues as $positionValue) {
            $result = $testInstance->setPositionXL($positionValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidXLPositionReturnsFalse()
    {
        $testInstance = new \PHPExcel\Chart\Legend;

        $result = $testInstance->setPositionXL(999);
        $this->assertFalse($result);
        //    Ensure that value is unchanged
        $result = $testInstance->getPositionXL();
        $this->assertEquals(\PHPExcel\Chart\Legend::XL_LEGEND_POSITION_RIGHT, $result);
    }

    public function testGetPositionXL()
    {
        $PositionValue = \PHPExcel\Chart\Legend::XL_LEGEND_POSITION_CORNER;

        $testInstance = new \PHPExcel\Chart\Legend;
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

        $testInstance = new \PHPExcel\Chart\Legend;

        foreach ($overlayValues as $overlayValue) {
            $result = $testInstance->setOverlay($overlayValue);
            $this->assertTrue($result);
        }
    }

    public function testSetInvalidOverlayReturnsFalse()
    {
        $testInstance = new \PHPExcel\Chart\Legend;

        $result = $testInstance->setOverlay('INVALID');
        $this->assertFalse($result);

        $result = $testInstance->getOverlay();
        $this->assertFalse($result);
    }

    public function testGetOverlay()
    {
        $OverlayValue = true;

        $testInstance = new \PHPExcel\Chart\Legend;
        $setValue = $testInstance->setOverlay($OverlayValue);

        $result = $testInstance->getOverlay();
        $this->assertEquals($OverlayValue, $result);
    }
}

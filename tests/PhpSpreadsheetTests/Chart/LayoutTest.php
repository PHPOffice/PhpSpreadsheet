<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testSetLayoutTarget(): void
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();

        $result = $testInstance->setLayoutTarget($LayoutTargetValue);
        self::assertInstanceOf(Layout::class, $result);
    }

    public function testGetLayoutTarget(): void
    {
        $LayoutTargetValue = 'String';

        $testInstance = new Layout();
        $testInstance->setLayoutTarget($LayoutTargetValue);

        $result = $testInstance->getLayoutTarget();
        self::assertEquals($LayoutTargetValue, $result);
    }

    public function testConstructorVsMethods(): void
    {
        $fillColor = new ChartColor('FF0000', 20, 'srgbClr');
        $borderColor = new ChartColor('accent1', 20, 'schemeClr');
        $fontColor = new ChartColor('red', 20, 'prstClr');
        $array = [
            'xMode' => 'factor',
            'yMode' => 'edge',
            'x' => 1.0,
            'y' => 2.0,
            'w' => 3.0,
            'h' => 4.0,
            'showVal' => true,
            'dLblPos' => 't',
            'numFmtCode' => '0.00%',
            'numFmtLinked' => true,
            'labelFillColor' => $fillColor,
            'labelBorderColor' => $borderColor,
            'labelFontColor' => $fontColor,
        ];
        $layout1 = new Layout($array);
        $layout2 = new Layout();
        $layout2
            ->setXMode('factor')
            ->setYMode('edge')
            ->setXposition(1.0)
            ->setYposition(2.0)
            ->setWidth(3.0)
            ->setHeight(4.0)
            ->setShowVal(true)
            ->setDLblPos('t')
            ->setNumFmtCode('0.00%')
            ->setNumFmtLinked(true)
            ->setLabelFillColor($fillColor)
            ->setLabelBorderColor($borderColor)
            ->setLabelFontColor($fontColor);
        self::assertEquals($layout1, $layout2);
    }
}

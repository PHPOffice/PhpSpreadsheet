<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testDefaultTypes(): void
    {
        $color = new ChartColor('800000');
        self::assertSame('srgbClr', $color->getType());
        self::assertSame('800000', $color->getValue());
        $color->setColorProperties('*accent1');
        self::assertSame('schemeClr', $color->getType());
        self::assertSame('accent1', $color->getValue());
        $color->setColorProperties('/red');
        self::assertSame('prstClr', $color->getType());
        self::assertSame('red', $color->getValue());
    }

    public function testDataSeriesValues(): void
    {
        $dsv = new DataSeriesValues();
        $dsv->setFillColor([new ChartColor(), new ChartColor()]);
        self::assertSame(['', ''], $dsv->getFillColor());
        $dsv->setFillColor('cccccc');
        self::assertSame('cccccc', $dsv->getFillColor());
    }
}

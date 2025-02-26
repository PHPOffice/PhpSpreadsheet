<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PHPUnit\Framework\TestCase;

class LineStylesTest extends TestCase
{
    public function testLineStyles(): void
    {
        $gridlines1 = new GridLines();
        $originalLineStyle = $gridlines1->getLineStyleArray();
        $gridlines1->setLineStyleProperties(
            3, // lineWidth
            Properties::LINE_STYLE_COMPOUND_DOUBLE, // compoundType
            '', // dashType
            Properties::LINE_STYLE_CAP_SQUARE, // capType
            '', // jointType
            '', // headArrowType
            0, // headArrowSize
            '', // endArrowType
            0, // endArrowSize
            'lg', // headArrowWidth
            'med', // headArrowLength
            '', // endArrowWidth
            '' // endArrowLength
        );
        $gridlines2 = new GridLines();
        $lineStyleProperties = [
            'width' => 3,
            'compound' => Properties::LINE_STYLE_COMPOUND_DOUBLE,
            'cap' => Properties::LINE_STYLE_CAP_SQUARE,
            'arrow' => ['head' => ['w' => 'lg', 'len' => 'med']],
        ];
        $gridlines2->setLineStyleArray($lineStyleProperties);
        self::assertSame($gridlines1->getLineStyleArray(), $gridlines2->getLineStyleArray());
        $gridlines2->setLineStyleArray(); // resets line styles
        self::assertSame($originalLineStyle, $gridlines2->getLineStyleArray());
    }
}

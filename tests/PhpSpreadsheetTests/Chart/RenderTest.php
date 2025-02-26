<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    public function testNoRenderer(): void
    {
        $chart = new Chart('Chart1');
        self::assertFalse($chart->render());
    }
}

<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface ChartEx
{
    /**
     * Get the ChartEx type.
     */
    public function getChartType(): string;

    /**
     * Refresh ChartEx data values.
     */
    public function refresh(Worksheet $worksheet): void;
}

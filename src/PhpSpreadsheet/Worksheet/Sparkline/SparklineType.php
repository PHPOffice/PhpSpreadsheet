<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Sparkline;

/**
 * The type of a sparkline group, as stored in the `type` attribute of the
 * `x14:sparklineGroup` element.
 */
enum SparklineType: string
{
    /** A line sparkline. */
    case Line = 'line';

    /** A column (bar) sparkline. */
    case Column = 'column';

    /**
     * A win/loss sparkline. Excel labels this "Win/Loss" in its UI, but the
     * underlying OOXML value is `stacked`.
     */
    case WinLoss = 'stacked';
}

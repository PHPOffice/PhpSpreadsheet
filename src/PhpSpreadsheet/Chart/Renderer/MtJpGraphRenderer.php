<?php

namespace PhpOffice\PhpSpreadsheet\Chart\Renderer;

/**
 * Jpgraph is not officially maintained by Composer at packagist.org.
 *
 * This renderer implementation uses package
 * https://packagist.org/packages/mitoteam/jpgraph
 *
 * This package is up to date for June 2023 and has PHP 8.2 support.
 */
class MtJpGraphRenderer extends JpGraphRendererBase
{
    protected static function init(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }

        \mitoteam\jpgraph\MtJpGraph::load([
            'bar',
            'contour',
            'line',
            'pie',
            'pie3d',
            'radar',
            'regstat',
            'scatter',
            'stock',
        ], true); // enable Extended mode

        $loaded = true;
    }
}

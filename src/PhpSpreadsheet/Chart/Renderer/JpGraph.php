<?php

namespace PhpOffice\PhpSpreadsheet\Chart\Renderer;

/**
 * Jpgraph is not oficially maintained in Composer, so the version there
 * could be out of date. For that reason, all unit test requiring Jpgraph
 * are skipped. So, do not measure code coverage for this class till that
 * is fixed.
 *
 * This implementation uses abandoned package
 * https://packagist.org/packages/jpgraph/jpgraph
 *
 * @codeCoverageIgnore
 */
class JpGraph extends JpGraphRendererBase
{
    protected static function init(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }

        // JpGraph is no longer included with distribution, but user may install it.
        // So Scrutinizer's complaint that it can't find it is reasonable, but unfixable.
        \JpGraph\JpGraph::load();
        \JpGraph\JpGraph::module('bar');
        \JpGraph\JpGraph::module('contour');
        \JpGraph\JpGraph::module('line');
        \JpGraph\JpGraph::module('pie');
        \JpGraph\JpGraph::module('pie3d');
        \JpGraph\JpGraph::module('radar');
        \JpGraph\JpGraph::module('regstat');
        \JpGraph\JpGraph::module('scatter');
        \JpGraph\JpGraph::module('stock');

        $loaded = true;
    }
}

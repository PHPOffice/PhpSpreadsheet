<?php

namespace PhpOffice\PhpSpreadsheet\Shared\OLE;

/**
 * An in-memory node used while constructing a CFB directory sibling tree.
 *
 * @internal
 */
final class PPSTreeNode
{
    /** @var ?self */
    public $left;

    /** @var ?self */
    public $right;

    /** @var int */
    public $color = 0;

    /** @var PPS */
    public $pps;

    /** @param PPS $pps */
    public function __construct($pps)
    {
        $this->pps = $pps;
    }
}

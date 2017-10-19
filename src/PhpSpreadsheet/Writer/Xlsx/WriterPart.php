<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class WriterPart
{
    /**
     * Parent Xlsx object.
     *
     * @var Xlsx
     */
    private $parentWriter;

    /**
     * Get parent Xlsx object.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return Xlsx
     */
    public function getParentWriter()
    {
        return $this->parentWriter;
    }

    /**
     * Set parent Xlsx object.
     *
     * @param Xlsx $pWriter
     */
    public function __construct(Xlsx $pWriter)
    {
        $this->parentWriter = $pWriter;
    }
}

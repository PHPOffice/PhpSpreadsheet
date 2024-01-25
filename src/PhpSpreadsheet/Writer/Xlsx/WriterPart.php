<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class WriterPart
{
    /**
     * Get parent Xlsx object.
     */
    public function getParentWriter(): Xlsx
    {
        return $this->parentWriter;
    }

    /**
     * Set parent Xlsx object.
     */
    public function __construct(
        /**
         * Parent Xlsx object.
         */
        private Xlsx $parentWriter
    ) {
    }
}

<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Writer\Ods;

abstract class WriterPart
{
    /**
     * Get Ods writer.
     */
    public function getParentWriter(): Ods
    {
        return $this->parentWriter;
    }

    /**
     * Set parent Ods writer.
     */
    public function __construct(
        /**
         * Parent Ods object.
         */
        private Ods $parentWriter
    ) {
    }

    abstract public function write(): string;
}

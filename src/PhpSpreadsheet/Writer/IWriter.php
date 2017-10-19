<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

interface IWriter
{
    /**
     * Save PhpSpreadsheet to file.
     *
     * @param string $pFilename Name of the file to save
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save($pFilename);
}

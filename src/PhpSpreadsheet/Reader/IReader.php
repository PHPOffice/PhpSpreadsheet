<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

interface IReader
{
    /**
     * IReader constructor.
     */
    public function __construct();

    /**
     * Can the current IReader read the file?
     *
     * @param string $pFilename
     *
     * @return bool
     */
    public function canRead($pFilename);

    /**
     * Loads PhpSpreadsheet from file.
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function load($pFilename);
}

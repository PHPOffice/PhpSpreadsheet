<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use ZipStream\Option\Archive;
use ZipStream\ZipStream;

/**
 * Either ZipStream2 or ZipStream3, but not both, may be used.
 * For code coverage testing, it will always be ZipStream3.
 *
 * @codeCoverageIgnore
 */
class ZipStream2
{
    /**
     * @param resource $fileHandle
     */
    public static function newZipStream($fileHandle): ZipStream
    {
        $options = new Archive();
        $options->setEnableZip64(false);
        $options->setOutputStream($fileHandle);

        return new ZipStream(null, $options);
    }
}

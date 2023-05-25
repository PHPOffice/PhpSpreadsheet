<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipStream0
{
    /**
     * @param resource $fileHandle
     */
    public static function newZipStream($fileHandle): ZipStream
    {
        return class_exists(Archive::class) ? ZipStream2::newZipStream($fileHandle) : ZipStream3::newZipStream($fileHandle);
    }
}

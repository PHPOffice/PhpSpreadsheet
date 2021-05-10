<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Shared\File;
use ZipArchive;

class ZipFileReader
{
    /**
     * @var ZipArchive
     */
    protected $archive;

    /**
     * @var XmlScanner
     */
    protected $securityScanner;

    public function __construct(ZipArchive $archive, XmlScanner $securityScanner)
    {
        $this->archive = $archive;
        $this->securityScanner = $securityScanner;
    }

    public function readRaw(string $fileName = ''): string
    {
        // Root-relative paths
        if (strpos($fileName, '//') !== false) {
            $fileName = substr($fileName, strpos($fileName, '//') + 1);
        }
        $fileName = File::realpath($fileName);

        // Sadly, some 3rd party xlsx generators don't use consistent case for filenaming
        //    so we need to load case-insensitively from the zip file

        // Apache POI fixes
        $contents = $this->archive->getFromName($fileName, 0, ZipArchive::FL_NOCASE);
        if ($contents === false) {
            $contents = $this->archive->getFromName(substr($fileName, 1), 0, ZipArchive::FL_NOCASE);
        }

        return $contents;
    }

    public function read(string $filename = ''): string
    {
        return $this->securityScanner->scan($this->readRaw($filename));
    }
}

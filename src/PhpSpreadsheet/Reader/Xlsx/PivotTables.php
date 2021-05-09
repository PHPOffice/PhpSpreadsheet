<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\PivotTable;
use PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition;
use PhpOffice\PhpSpreadsheet\PivotTable\PivotCacheDefinition\PivotCacheRecords;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use ZipArchive;

class PivotTables
{
    /**
     * @var Worksheet
     */
    private $worksheet;

    /**
     * @var ZipArchive
     */
    private $archive;

    public function __construct(Worksheet $worksheet, ZipArchive $archive)
    {
        $this->worksheet = $worksheet;
        $this->archive = $archive;
    }

    public function load(SimpleXMLElement $relsWorksheet, string $dir, string $fileWorksheet): void
    {
        foreach ($relsWorksheet->Relationship as $ele) {
            if ($ele['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/pivotTable') {
                $objPivotTable = new PivotTable(
                    (string) $ele['Id'],
                    (string) $ele['Target'],
                    $this->getFromZipArchive(dirname("$dir/$fileWorksheet") . '/' . (string) $ele['Target'])
                );

                $relsPivotTablePath = $dir . '/pivotTables/_rels/' . $objPivotTable->getName() . '.rels';
                if ($this->archive->locateName($relsPivotTablePath)) {
                    $relsPivotTable = simplexml_load_string($this->getFromZipArchive($relsPivotTablePath));
                    foreach ($relsPivotTable->Relationship as $elePT) {
                        if ($elePT['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/pivotCacheDefinition') {
                            $objPivotCacheDefinition = new PivotCacheDefinition(
                                (string) $elePT['Id'],
                                (string) $elePT['Target'],
                                $this->getFromZipArchive(dirname("$dir/$fileWorksheet") . '/' . (string) $elePT['Target'])
                            );

                            $relsPivotCachePath = $dir . '/pivotCache/_rels/' . $objPivotCacheDefinition->getName() . '.rels';
                            if ($this->archive->locateName($relsPivotCachePath)) {
                                $relsPivotCache = simplexml_load_string($this->getFromZipArchive($relsPivotCachePath));
                                foreach ($relsPivotCache->Relationship as $elePC) {
                                    if ($elePC['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/pivotCacheRecords') {
                                        $objPivotCacheRecords = new PivotCacheRecords(
                                            (string) $elePC['Id'],
                                            (string) $elePC['Target'],
                                            $this->getFromZipArchive(dirname("$dir/$fileWorksheet") . '/' . dirname((string) $elePT['Target']) . '/' . (string) $elePC['Target'])
                                        );
                                    }
                                    $objPivotCacheDefinition->addPivotCacheRecords($objPivotCacheRecords);
                                }
                            }
                        }
                        $objPivotTable->addPivotCacheDefinition($objPivotCacheDefinition);
                    }
                }

                $this->worksheet->addPivotTable($objPivotTable);
            }
        }
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFromZipArchive($fileName = '')
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
}

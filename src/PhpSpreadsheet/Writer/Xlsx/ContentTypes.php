<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class ContentTypes extends WriterPart
{
    /**
     * Write content types to XML format.
     *
     * @param bool $includeCharts Flag indicating if we should include drawing details for charts
     *
     * @return string XML Output
     */
    public function writeContentTypes(Spreadsheet $spreadsheet, $includeCharts = false)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Types
        $objWriter->startElement('Types');
        $objWriter->writeAttribute('xmlns', Namespaces::CONTENT_TYPES);

        // Theme
        $this->writeOverrideContentType($objWriter, '/xl/theme/theme1.xml', 'application/vnd.openxmlformats-officedocument.theme+xml');

        // Styles
        $this->writeOverrideContentType($objWriter, '/xl/styles.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml');

        // Rels
        $this->writeDefaultContentType($objWriter, 'rels', 'application/vnd.openxmlformats-package.relationships+xml');

        // XML
        $this->writeDefaultContentType($objWriter, 'xml', 'application/xml');

        // VML
        $this->writeDefaultContentType($objWriter, 'vml', 'application/vnd.openxmlformats-officedocument.vmlDrawing');

        // Workbook
        if ($spreadsheet->hasMacros()) { //Macros in workbook ?
            // Yes : not standard content but "macroEnabled"
            $this->writeOverrideContentType($objWriter, '/xl/workbook.xml', 'application/vnd.ms-excel.sheet.macroEnabled.main+xml');
            //... and define a new type for the VBA project
            // Better use Override, because we can use 'bin' also for xl\printerSettings\printerSettings1.bin
            $this->writeOverrideContentType($objWriter, '/xl/vbaProject.bin', 'application/vnd.ms-office.vbaProject');
            if ($spreadsheet->hasMacrosCertificate()) {
                // signed macros ?
                // Yes : add needed information
                $this->writeOverrideContentType($objWriter, '/xl/vbaProjectSignature.bin', 'application/vnd.ms-office.vbaProjectSignature');
            }
        } else {
            // no macros in workbook, so standard type
            $this->writeOverrideContentType($objWriter, '/xl/workbook.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml');
        }

        // DocProps
        $this->writeOverrideContentType($objWriter, '/docProps/app.xml', 'application/vnd.openxmlformats-officedocument.extended-properties+xml');

        $this->writeOverrideContentType($objWriter, '/docProps/core.xml', 'application/vnd.openxmlformats-package.core-properties+xml');

        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (!empty($customPropertyList)) {
            $this->writeOverrideContentType($objWriter, '/docProps/custom.xml', 'application/vnd.openxmlformats-officedocument.custom-properties+xml');
        }

        // Worksheets
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $this->writeOverrideContentType($objWriter, '/xl/worksheets/sheet' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml');
        }

        // Shared strings
        $this->writeOverrideContentType($objWriter, '/xl/sharedStrings.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml');

        // Table
        $table = 1;
        for ($i = 0; $i < $sheetCount; ++$i) {
            $tableCount = $spreadsheet->getSheet($i)->getTableCollection()->count();

            for ($t = 1; $t <= $tableCount; ++$t) {
                $this->writeOverrideContentType($objWriter, '/xl/tables/table' . $table++ . '.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.table+xml');
            }
        }

        // Add worksheet relationship content types
        $unparsedLoadedData = $spreadsheet->getUnparsedLoadedData();
        $chart = 1;
        for ($i = 0; $i < $sheetCount; ++$i) {
            $drawings = $spreadsheet->getSheet($i)->getDrawingCollection();
            $drawingCount = count($drawings);
            $chartCount = ($includeCharts) ? $spreadsheet->getSheet($i)->getChartCount() : 0;
            $hasUnparsedDrawing = isset($unparsedLoadedData['sheets'][$spreadsheet->getSheet($i)->getCodeName()]['drawingOriginalIds']);

            //    We need a drawing relationship for the worksheet if we have either drawings or charts
            if (($drawingCount > 0) || ($chartCount > 0) || $hasUnparsedDrawing) {
                $this->writeOverrideContentType($objWriter, '/xl/drawings/drawing' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.drawing+xml');
            }

            //    If we have charts, then we need a chart relationship for every individual chart
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $this->writeOverrideContentType($objWriter, '/xl/charts/chart' . $chart++ . '.xml', 'application/vnd.openxmlformats-officedocument.drawingml.chart+xml');
                }
            }
        }

        // Comments
        for ($i = 0; $i < $sheetCount; ++$i) {
            if (count($spreadsheet->getSheet($i)->getComments()) > 0) {
                $this->writeOverrideContentType($objWriter, '/xl/comments' . ($i + 1) . '.xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.comments+xml');
            }
        }

        // Add media content-types
        $aMediaContentTypes = [];
        $mediaCount = $this->getParentWriter()->getDrawingHashTable()->count();
        for ($i = 0; $i < $mediaCount; ++$i) {
            $extension = '';
            $mimeType = '';

            if ($this->getParentWriter()->getDrawingHashTable()->getByIndex($i) instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
                $extension = strtolower($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getExtension());
                $mimeType = $this->getImageMimeType($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getPath());
            } elseif ($this->getParentWriter()->getDrawingHashTable()->getByIndex($i) instanceof MemoryDrawing) {
                $extension = strtolower($this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getMimeType());
                $extension = explode('/', $extension);
                $extension = $extension[1];

                $mimeType = $this->getParentWriter()->getDrawingHashTable()->getByIndex($i)->getMimeType();
            }

            if (!isset($aMediaContentTypes[$extension])) {
                $aMediaContentTypes[$extension] = $mimeType;

                $this->writeDefaultContentType($objWriter, $extension, $mimeType);
            }
        }
        if ($spreadsheet->hasRibbonBinObjects()) {
            // Some additional objects in the ribbon ?
            // we need to write "Extension" but not already write for media content
            $tabRibbonTypes = array_diff($spreadsheet->getRibbonBinObjects('types') ?? [], array_keys($aMediaContentTypes));
            foreach ($tabRibbonTypes as $aRibbonType) {
                $mimeType = 'image/.' . $aRibbonType; //we wrote $mimeType like customUI Editor
                $this->writeDefaultContentType($objWriter, $aRibbonType, $mimeType);
            }
        }
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            if (count($spreadsheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
                foreach ($spreadsheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
                    if (!isset($aMediaContentTypes[strtolower($image->getExtension())])) {
                        $aMediaContentTypes[strtolower($image->getExtension())] = $this->getImageMimeType($image->getPath());

                        $this->writeDefaultContentType($objWriter, strtolower($image->getExtension()), $aMediaContentTypes[strtolower($image->getExtension())]);
                    }
                }
            }

            if (count($spreadsheet->getSheet($i)->getComments()) > 0) {
                foreach ($spreadsheet->getSheet($i)->getComments() as $comment) {
                    if (!$comment->hasBackgroundImage()) {
                        continue;
                    }

                    $bgImage = $comment->getBackgroundImage();
                    $bgImageExtentionKey = strtolower($bgImage->getImageFileExtensionForSave(false));

                    if (!isset($aMediaContentTypes[$bgImageExtentionKey])) {
                        $aMediaContentTypes[$bgImageExtentionKey] = $bgImage->getImageMimeType();

                        $this->writeDefaultContentType($objWriter, $bgImageExtentionKey, $aMediaContentTypes[$bgImageExtentionKey]);
                    }
                }
            }
        }

        // unparsed defaults
        if (isset($unparsedLoadedData['default_content_types'])) {
            foreach ($unparsedLoadedData['default_content_types'] as $extName => $contentType) {
                $this->writeDefaultContentType($objWriter, $extName, $contentType);
            }
        }

        // unparsed overrides
        if (isset($unparsedLoadedData['override_content_types'])) {
            foreach ($unparsedLoadedData['override_content_types'] as $partName => $overrideType) {
                $this->writeOverrideContentType($objWriter, $partName, $overrideType);
            }
        }

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    private static int $three = 3; // phpstan silliness

    /**
     * Get image mime type.
     *
     * @param string $filename Filename
     *
     * @return string Mime Type
     */
    private function getImageMimeType($filename)
    {
        if (File::fileExists($filename)) {
            $image = getimagesize($filename);

            return image_type_to_mime_type((is_array($image) && count($image) >= self::$three) ? $image[2] : 0);
        }

        throw new WriterException("File $filename does not exist");
    }

    /**
     * Write Default content type.
     *
     * @param string $partName Part name
     * @param string $contentType Content type
     */
    private function writeDefaultContentType(XMLWriter $objWriter, $partName, $contentType): void
    {
        if ($partName != '' && $contentType != '') {
            // Write content type
            $objWriter->startElement('Default');
            $objWriter->writeAttribute('Extension', $partName);
            $objWriter->writeAttribute('ContentType', $contentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    /**
     * Write Override content type.
     *
     * @param string $partName Part name
     * @param string $contentType Content type
     */
    private function writeOverrideContentType(XMLWriter $objWriter, $partName, $contentType): void
    {
        if ($partName != '' && $contentType != '') {
            // Write content type
            $objWriter->startElement('Override');
            $objWriter->writeAttribute('PartName', $partName);
            $objWriter->writeAttribute('ContentType', $contentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }
}

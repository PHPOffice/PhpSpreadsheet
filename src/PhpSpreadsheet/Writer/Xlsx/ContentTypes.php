<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class ContentTypes extends WriterPart
{
    /**
     * Write content types to XML format.
     *
     * @param Spreadsheet $spreadsheet
     * @param bool $includeCharts Flag indicating if we should include drawing details for charts
     *
     * @throws WriterException
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
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/content-types');

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
            $this->writeDefaultContentType($objWriter, 'bin', 'application/vnd.ms-office.vbaProject');
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

        // Add worksheet relationship content types
        $chart = 1;
        for ($i = 0; $i < $sheetCount; ++$i) {
            $drawings = $spreadsheet->getSheet($i)->getDrawingCollection();
            $drawingCount = count($drawings);
            $chartCount = ($includeCharts) ? $spreadsheet->getSheet($i)->getChartCount() : 0;

            //    We need a drawing relationship for the worksheet if we have either drawings or charts
            if (($drawingCount > 0) || ($chartCount > 0)) {
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
            $tabRibbonTypes = array_diff($spreadsheet->getRibbonBinObjects('types'), array_keys($aMediaContentTypes));
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
        }

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Get image mime type.
     *
     * @param string $pFile Filename
     *
     * @throws WriterException
     *
     * @return string Mime Type
     */
    private function getImageMimeType($pFile)
    {
        if (File::fileExists($pFile)) {
            $image = getimagesize($pFile);

            return image_type_to_mime_type($image[2]);
        }
        throw new WriterException("File $pFile does not exist");
    }

    /**
     * Write Default content type.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param string $pPartname Part name
     * @param string $pContentType Content type
     *
     * @throws WriterException
     */
    private function writeDefaultContentType(XMLWriter $objWriter, $pPartname, $pContentType)
    {
        if ($pPartname != '' && $pContentType != '') {
            // Write content type
            $objWriter->startElement('Default');
            $objWriter->writeAttribute('Extension', $pPartname);
            $objWriter->writeAttribute('ContentType', $pContentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    /**
     * Write Override content type.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param string $pPartname Part name
     * @param string $pContentType Content type
     *
     * @throws WriterException
     */
    private function writeOverrideContentType(XMLWriter $objWriter, $pPartname, $pContentType)
    {
        if ($pPartname != '' && $pContentType != '') {
            // Write content type
            $objWriter->startElement('Override');
            $objWriter->writeAttribute('PartName', $pPartname);
            $objWriter->writeAttribute('ContentType', $pContentType);
            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }
}

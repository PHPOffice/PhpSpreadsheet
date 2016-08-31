<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Excel2007;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Rels extends WriterPart
{
    /**
     * Write relationships to XML format
     *
     * @param \PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return string  XML Output
     */
    public function writeRelationships(\PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        $customPropertyList = $spreadsheet->getProperties()->getCustomProperties();
        if (!empty($customPropertyList)) {
            // Relationship docProps/app.xml
            $this->writeRelationship(
                $objWriter,
                4,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties',
                'docProps/custom.xml'
            );
        }

        // Relationship docProps/app.xml
        $this->writeRelationship(
            $objWriter,
            3,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
            'docProps/app.xml'
        );

        // Relationship docProps/core.xml
        $this->writeRelationship(
            $objWriter,
            2,
            'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
            'docProps/core.xml'
        );

        // Relationship xl/workbook.xml
        $this->writeRelationship(
            $objWriter,
            1,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
            'xl/workbook.xml'
        );
        // a custom UI in workbook ?
        if ($spreadsheet->hasRibbon()) {
            $this->writeRelationShip(
                $objWriter,
                5,
                'http://schemas.microsoft.com/office/2006/relationships/ui/extensibility',
                $spreadsheet->getRibbonXMLData('target')
            );
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write workbook relationships to XML format
     *
     * @param \PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return string  XML Output
     */
    public function writeWorkbookRelationships(\PhpOffice\PhpSpreadsheet\SpreadSheet $spreadsheet = null)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Relationship styles.xml
        $this->writeRelationship(
            $objWriter,
            1,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles',
            'styles.xml'
        );

        // Relationship theme/theme1.xml
        $this->writeRelationship(
            $objWriter,
            2,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme',
            'theme/theme1.xml'
        );

        // Relationship sharedStrings.xml
        $this->writeRelationship(
            $objWriter,
            3,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings',
            'sharedStrings.xml'
        );

        // Relationships with sheets
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $this->writeRelationship(
                $objWriter,
                ($i + 1 + 3),
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet',
                'worksheets/sheet' . ($i + 1) . '.xml'
            );
        }
        // Relationships for vbaProject if needed
        // id : just after the last sheet
        if ($spreadsheet->hasMacros()) {
            $this->writeRelationShip(
                $objWriter,
                ($i + 1 + 3),
                'http://schemas.microsoft.com/office/2006/relationships/vbaProject',
                'vbaProject.bin'
            );
            ++$i; //increment i if needed for an another relation
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write worksheet relationships to XML format
     *
     * Numbering is as follows:
     *     rId1                 - Drawings
     *  rId_hyperlink_x     - Hyperlinks
     *
     * @param     \PhpOffice\PhpSpreadsheet\Worksheet    $pWorksheet
     * @param     int                    $pWorksheetId
     * @param    bool                $includeCharts    Flag indicating if we should write charts
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return string          XML Output
     */
    public function writeWorksheetRelationships(\PhpOffice\PhpSpreadsheet\Worksheet $pWorksheet = null, $pWorksheetId = 1, $includeCharts = false)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Write drawing relationships?
        $d = 0;
        if ($includeCharts) {
            $charts = $pWorksheet->getChartCollection();
        } else {
            $charts = [];
        }
        if (($pWorksheet->getDrawingCollection()->count() > 0) ||
            (count($charts) > 0)) {
            $this->writeRelationship(
                $objWriter,
                ++$d,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing',
                '../drawings/drawing' . $pWorksheetId . '.xml'
            );
        }

        // Write hyperlink relationships?
        $i = 1;
        foreach ($pWorksheet->getHyperlinkCollection() as $hyperlink) {
            if (!$hyperlink->isInternal()) {
                $this->writeRelationship(
                    $objWriter,
                    '_hyperlink_' . $i,
                    'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
                    $hyperlink->getUrl(),
                    'External'
                );

                ++$i;
            }
        }

        // Write comments relationship?
        $i = 1;
        if (count($pWorksheet->getComments()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_comments_vml' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
                '../drawings/vmlDrawing' . $pWorksheetId . '.vml'
            );

            $this->writeRelationship(
                $objWriter,
                '_comments' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments',
                '../comments' . $pWorksheetId . '.xml'
            );
        }

        // Write header/footer relationship?
        $i = 1;
        if (count($pWorksheet->getHeaderFooter()->getImages()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_headerfooter_vml' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
                '../drawings/vmlDrawingHF' . $pWorksheetId . '.vml'
            );
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write drawing relationships to XML format
     *
     * @param     \PhpOffice\PhpSpreadsheet\Worksheet    $pWorksheet
     * @param    int                    &$chartRef        Chart ID
     * @param    bool                $includeCharts    Flag indicating if we should write charts
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return string          XML Output
     */
    public function writeDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet $pWorksheet, &$chartRef, $includeCharts = false)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Loop through images and write relationships
        $i = 1;
        $iterator = $pWorksheet->getDrawingCollection()->getIterator();
        while ($iterator->valid()) {
            if ($iterator->current() instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing
                || $iterator->current() instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
                // Write relationship for image drawing
                $this->writeRelationship(
                    $objWriter,
                    $i,
                    'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
                    '../media/' . str_replace(' ', '', $iterator->current()->getIndexedFilename())
                );
            }

            $iterator->next();
            ++$i;
        }

        if ($includeCharts) {
            // Loop through charts and write relationships
            $chartCount = $pWorksheet->getChartCount();
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $this->writeRelationship(
                        $objWriter,
                        $i++,
                        'http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart',
                        '../charts/chart' . ++$chartRef . '.xml'
                    );
                }
            }
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write header/footer drawing relationships to XML format
     *
     * @param     \PhpOffice\PhpSpreadsheet\Worksheet            $pWorksheet
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return string                  XML Output
     */
    public function writeHeaderFooterDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet $pWorksheet = null)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new \PhpOffice\PhpSpreadsheet\Shared\XMLWriter(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Loop through images and write relationships
        foreach ($pWorksheet->getHeaderFooter()->getImages() as $key => $value) {
            // Write relationship for image drawing
            $this->writeRelationship(
                $objWriter,
                $key,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
                '../media/' . $value->getIndexedFilename()
            );
        }

        $objWriter->endElement();

        return $objWriter->getData();
    }

    /**
     * Write Override content type
     *
     * @param     \PhpOffice\PhpSpreadsheet\Shared\XMLWriter     $objWriter         XML Writer
     * @param     int                            $pId            Relationship ID. rId will be prepended!
     * @param     string                        $pType            Relationship type
     * @param     string                         $pTarget        Relationship target
     * @param     string                         $pTargetMode    Relationship target mode
     * @throws     \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function writeRelationship(\PhpOffice\PhpSpreadsheet\Shared\XMLWriter $objWriter = null, $pId = 1, $pType = '', $pTarget = '', $pTargetMode = '')
    {
        if ($pType != '' && $pTarget != '') {
            // Write relationship
            $objWriter->startElement('Relationship');
            $objWriter->writeAttribute('Id', 'rId' . $pId);
            $objWriter->writeAttribute('Type', $pType);
            $objWriter->writeAttribute('Target', $pTarget);

            if ($pTargetMode != '') {
                $objWriter->writeAttribute('TargetMode', $pTargetMode);
            }

            $objWriter->endElement();
        } else {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('Invalid parameters passed.');
        }
    }
}

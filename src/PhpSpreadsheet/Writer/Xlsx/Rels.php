<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Rels extends WriterPart
{
    /**
     * Write relationships to XML format.
     *
     * @return string XML Output
     */
    public function writeRelationships(Spreadsheet $spreadsheet)
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
     * Write workbook relationships to XML format.
     *
     * @return string XML Output
     */
    public function writeWorkbookRelationships(Spreadsheet $spreadsheet)
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
     * Write worksheet relationships to XML format.
     *
     * Numbering is as follows:
     *     rId1                 - Drawings
     *  rId_hyperlink_x     - Hyperlinks
     *
     * @param int $worksheetId
     * @param bool $includeCharts Flag indicating if we should write charts
     *
     * @return string XML Output
     */
    public function writeWorksheetRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, $worksheetId = 1, $includeCharts = false)
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

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Write drawing relationships?
        $drawingOriginalIds = [];
        $unparsedLoadedData = $worksheet->getParent()->getUnparsedLoadedData();
        if (isset($unparsedLoadedData['sheets'][$worksheet->getCodeName()]['drawingOriginalIds'])) {
            $drawingOriginalIds = $unparsedLoadedData['sheets'][$worksheet->getCodeName()]['drawingOriginalIds'];
        }

        if ($includeCharts) {
            $charts = $worksheet->getChartCollection();
        } else {
            $charts = [];
        }

        if (($worksheet->getDrawingCollection()->count() > 0) || (count($charts) > 0) || $drawingOriginalIds) {
            $rId = 1;

            // Use original $relPath to get original $rId.
            // Take first. In future can be overwritten.
            // (! synchronize with \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet::writeDrawings)
            reset($drawingOriginalIds);
            $relPath = key($drawingOriginalIds);
            if (isset($drawingOriginalIds[$relPath])) {
                $rId = (int) (substr($drawingOriginalIds[$relPath], 3));
            }

            // Generate new $relPath to write drawing relationship
            $relPath = '../drawings/drawing' . $worksheetId . '.xml';
            $this->writeRelationship(
                $objWriter,
                $rId,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing',
                $relPath
            );
        }

        // Write hyperlink relationships?
        $i = 1;
        foreach ($worksheet->getHyperlinkCollection() as $hyperlink) {
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
        if (count($worksheet->getComments()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_comments_vml' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
                '../drawings/vmlDrawing' . $worksheetId . '.vml'
            );

            $this->writeRelationship(
                $objWriter,
                '_comments' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments',
                '../comments' . $worksheetId . '.xml'
            );
        }

        // Write header/footer relationship?
        $i = 1;
        if (count($worksheet->getHeaderFooter()->getImages()) > 0) {
            $this->writeRelationship(
                $objWriter,
                '_headerfooter_vml' . $i,
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing',
                '../drawings/vmlDrawingHF' . $worksheetId . '.vml'
            );
        }

        $this->writeUnparsedRelationship($worksheet, $objWriter, 'ctrlProps', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/ctrlProp');
        $this->writeUnparsedRelationship($worksheet, $objWriter, 'vmlDrawings', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing');
        $this->writeUnparsedRelationship($worksheet, $objWriter, 'printerSettings', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/printerSettings');

        $objWriter->endElement();

        return $objWriter->getData();
    }

    private function writeUnparsedRelationship(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, XMLWriter $objWriter, $relationship, $type): void
    {
        $unparsedLoadedData = $worksheet->getParent()->getUnparsedLoadedData();
        if (!isset($unparsedLoadedData['sheets'][$worksheet->getCodeName()][$relationship])) {
            return;
        }

        foreach ($unparsedLoadedData['sheets'][$worksheet->getCodeName()][$relationship] as $rId => $value) {
            $this->writeRelationship(
                $objWriter,
                $rId,
                $type,
                $value['relFilePath']
            );
        }
    }

    /**
     * Write drawing relationships to XML format.
     *
     * @param int $chartRef Chart ID
     * @param bool $includeCharts Flag indicating if we should write charts
     *
     * @return string XML Output
     */
    public function writeDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, &$chartRef, $includeCharts = false)
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

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Loop through images and write relationships
        $i = 1;
        $iterator = $worksheet->getDrawingCollection()->getIterator();
        while ($iterator->valid()) {
            $drawing = $iterator->current();
            if (
                $drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing
                || $drawing instanceof MemoryDrawing
            ) {
                // Write relationship for image drawing
                $this->writeRelationship(
                    $objWriter,
                    $i,
                    'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
                    '../media/' . str_replace(' ', '', $drawing->getIndexedFilename())
                );

                $i = $this->writeDrawingHyperLink($objWriter, $drawing, $i);
            }

            $iterator->next();
            ++$i;
        }

        if ($includeCharts) {
            // Loop through charts and write relationships
            $chartCount = $worksheet->getChartCount();
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
     * Write header/footer drawing relationships to XML format.
     *
     * @return string XML Output
     */
    public function writeHeaderFooterDrawingRelationships(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet)
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

        // Relationships
        $objWriter->startElement('Relationships');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

        // Loop through images and write relationships
        foreach ($worksheet->getHeaderFooter()->getImages() as $key => $value) {
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
     * Write Override content type.
     *
     * @param int $id Relationship ID. rId will be prepended!
     * @param string $type Relationship type
     * @param string $target Relationship target
     * @param string $targetMode Relationship target mode
     */
    private function writeRelationship(XMLWriter $objWriter, $id, $type, $target, $targetMode = ''): void
    {
        if ($type != '' && $target != '') {
            // Write relationship
            $objWriter->startElement('Relationship');
            $objWriter->writeAttribute('Id', 'rId' . $id);
            $objWriter->writeAttribute('Type', $type);
            $objWriter->writeAttribute('Target', $target);

            if ($targetMode != '') {
                $objWriter->writeAttribute('TargetMode', $targetMode);
            }

            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    private function writeDrawingHyperLink(XMLWriter $objWriter, BaseDrawing $drawing, int $i): int
    {
        if ($drawing->getHyperlink() === null) {
            return $i;
        }

        ++$i;
        $this->writeRelationship(
            $objWriter,
            $i,
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
            $drawing->getHyperlink()->getUrl(),
            $drawing->getHyperlink()->getTypeHyperlink()
        );

        return $i;
    }
}

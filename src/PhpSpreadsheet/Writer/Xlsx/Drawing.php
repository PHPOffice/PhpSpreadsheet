<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Shared\Drawing as SharedDrawing;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Drawing extends WriterPart
{
    /**
     * Write drawings to XML format.
     *
     * @param bool $includeCharts Flag indicating if we should include drawing details for charts
     *
     * @return string XML Output
     */
    public function writeDrawings(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, bool $includeCharts = false): string
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

        // xdr:wsDr
        $objWriter->startElement('xdr:wsDr');
        $objWriter->writeAttribute('xmlns:xdr', Namespaces::SPREADSHEET_DRAWING);
        $objWriter->writeAttribute('xmlns:a', Namespaces::DRAWINGML);

        // Loop through images and write drawings
        $i = 1;
        $iterator = $worksheet->getDrawingCollection()->getIterator();
        while ($iterator->valid()) {
            /** @var BaseDrawing $pDrawing */
            $pDrawing = $iterator->current();
            $pRelationId = $i;
            $hlinkClickId = $pDrawing->getHyperlink() === null ? null : ++$i;

            $this->writeDrawing($objWriter, $pDrawing, $pRelationId, $hlinkClickId);

            $iterator->next();
            ++$i;
        }

        if ($includeCharts) {
            $chartCount = $worksheet->getChartCount();
            // Loop through charts and write the chart position
            if ($chartCount > 0) {
                for ($c = 0; $c < $chartCount; ++$c) {
                    $chart = $worksheet->getChartByIndex((string) $c);
                    if ($chart !== false) {
                        $this->writeChart($objWriter, $chart, $c + $i);
                    }
                }
            }
        }

        // unparsed AlternateContent
        $unparsedLoadedData = $worksheet->getParentOrThrow()->getUnparsedLoadedData();
        if (isset($unparsedLoadedData['sheets'][$worksheet->getCodeName()]['drawingAlternateContents'])) {
            foreach ($unparsedLoadedData['sheets'][$worksheet->getCodeName()]['drawingAlternateContents'] as $drawingAlternateContent) {
                $objWriter->writeRaw($drawingAlternateContent);
            }
        }

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write drawings to XML format.
     */
    public function writeChart(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Chart\Chart $chart, int $relationId = -1): void
    {
        $tl = $chart->getTopLeftPosition();
        $tlColRow = Coordinate::indexesFromString($tl['cell']);
        $br = $chart->getBottomRightPosition();

        $isTwoCellAnchor = $br['cell'] !== '';
        if ($isTwoCellAnchor) {
            $brColRow = Coordinate::indexesFromString($br['cell']);

            $objWriter->startElement('xdr:twoCellAnchor');

            $objWriter->startElement('xdr:from');
            $objWriter->writeElement('xdr:col', (string) ($tlColRow[0] - 1));
            $objWriter->writeElement('xdr:colOff', self::stringEmu($tl['xOffset']));
            $objWriter->writeElement('xdr:row', (string) ($tlColRow[1] - 1));
            $objWriter->writeElement('xdr:rowOff', self::stringEmu($tl['yOffset']));
            $objWriter->endElement();
            $objWriter->startElement('xdr:to');
            $objWriter->writeElement('xdr:col', (string) ($brColRow[0] - 1));
            $objWriter->writeElement('xdr:colOff', self::stringEmu($br['xOffset']));
            $objWriter->writeElement('xdr:row', (string) ($brColRow[1] - 1));
            $objWriter->writeElement('xdr:rowOff', self::stringEmu($br['yOffset']));
            $objWriter->endElement();
        } elseif ($chart->getOneCellAnchor()) {
            $objWriter->startElement('xdr:oneCellAnchor');

            $objWriter->startElement('xdr:from');
            $objWriter->writeElement('xdr:col', (string) ($tlColRow[0] - 1));
            $objWriter->writeElement('xdr:colOff', self::stringEmu($tl['xOffset']));
            $objWriter->writeElement('xdr:row', (string) ($tlColRow[1] - 1));
            $objWriter->writeElement('xdr:rowOff', self::stringEmu($tl['yOffset']));
            $objWriter->endElement();
            $objWriter->startElement('xdr:ext');
            $objWriter->writeAttribute('cx', self::stringEmu($br['xOffset']));
            $objWriter->writeAttribute('cy', self::stringEmu($br['yOffset']));
            $objWriter->endElement();
        } else {
            $objWriter->startElement('xdr:absoluteAnchor');
            $objWriter->startElement('xdr:pos');
            $objWriter->writeAttribute('x', '0');
            $objWriter->writeAttribute('y', '0');
            $objWriter->endElement();
            $objWriter->startElement('xdr:ext');
            $objWriter->writeAttribute('cx', self::stringEmu($br['xOffset']));
            $objWriter->writeAttribute('cy', self::stringEmu($br['yOffset']));
            $objWriter->endElement();
        }

        $objWriter->startElement('xdr:graphicFrame');
        $objWriter->writeAttribute('macro', '');
        $objWriter->startElement('xdr:nvGraphicFramePr');
        $objWriter->startElement('xdr:cNvPr');
        $objWriter->writeAttribute('name', 'Chart ' . $relationId);
        $objWriter->writeAttribute('id', (string) (1025 * $relationId));
        $objWriter->endElement();
        $objWriter->startElement('xdr:cNvGraphicFramePr');
        $objWriter->startElement('a:graphicFrameLocks');
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('xdr:xfrm');
        $objWriter->startElement('a:off');
        $objWriter->writeAttribute('x', '0');
        $objWriter->writeAttribute('y', '0');
        $objWriter->endElement();
        $objWriter->startElement('a:ext');
        $objWriter->writeAttribute('cx', '0');
        $objWriter->writeAttribute('cy', '0');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('a:graphic');
        $objWriter->startElement('a:graphicData');
        $objWriter->writeAttribute('uri', Namespaces::CHART);
        $objWriter->startElement('c:chart');
        $objWriter->writeAttribute('xmlns:c', Namespaces::CHART);
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
        $objWriter->writeAttribute('r:id', 'rId' . $relationId);
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('xdr:clientData');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write drawings to XML format.
     */
    public function writeDrawing(XMLWriter $objWriter, BaseDrawing $drawing, int $relationId = -1, ?int $hlinkClickId = null): void
    {
        if ($relationId >= 0) {
            $isTwoCellAnchor = $drawing->getCoordinates2() !== '';
            if ($isTwoCellAnchor) {
                // xdr:twoCellAnchor
                $objWriter->startElement('xdr:twoCellAnchor');
                if ($drawing->validEditAs()) {
                    $objWriter->writeAttribute('editAs', $drawing->getEditAs());
                }
                // Image location
                $aCoordinates = Coordinate::indexesFromString($drawing->getCoordinates());
                $aCoordinates2 = Coordinate::indexesFromString($drawing->getCoordinates2());

                // xdr:from
                $objWriter->startElement('xdr:from');
                $objWriter->writeElement('xdr:col', (string) ($aCoordinates[0] - 1));
                $objWriter->writeElement('xdr:colOff', self::stringEmu($drawing->getOffsetX()));
                $objWriter->writeElement('xdr:row', (string) ($aCoordinates[1] - 1));
                $objWriter->writeElement('xdr:rowOff', self::stringEmu($drawing->getOffsetY()));
                $objWriter->endElement();

                // xdr:to
                $objWriter->startElement('xdr:to');
                $objWriter->writeElement('xdr:col', (string) ($aCoordinates2[0] - 1));
                $objWriter->writeElement('xdr:colOff', self::stringEmu($drawing->getOffsetX2()));
                $objWriter->writeElement('xdr:row', (string) ($aCoordinates2[1] - 1));
                $objWriter->writeElement('xdr:rowOff', self::stringEmu($drawing->getOffsetY2()));
                $objWriter->endElement();
            } else {
                // xdr:oneCellAnchor
                $objWriter->startElement('xdr:oneCellAnchor');
                // Image location
                $aCoordinates = Coordinate::indexesFromString($drawing->getCoordinates());

                // xdr:from
                $objWriter->startElement('xdr:from');
                $objWriter->writeElement('xdr:col', (string) ($aCoordinates[0] - 1));
                $objWriter->writeElement('xdr:colOff', self::stringEmu($drawing->getOffsetX()));
                $objWriter->writeElement('xdr:row', (string) ($aCoordinates[1] - 1));
                $objWriter->writeElement('xdr:rowOff', self::stringEmu($drawing->getOffsetY()));
                $objWriter->endElement();

                // xdr:ext
                $objWriter->startElement('xdr:ext');
                $objWriter->writeAttribute('cx', self::stringEmu($drawing->getWidth()));
                $objWriter->writeAttribute('cy', self::stringEmu($drawing->getHeight()));
                $objWriter->endElement();
            }

            // xdr:pic
            $objWriter->startElement('xdr:pic');

            // xdr:nvPicPr
            $objWriter->startElement('xdr:nvPicPr');

            // xdr:cNvPr
            $objWriter->startElement('xdr:cNvPr');
            $objWriter->writeAttribute('id', (string) $relationId);
            $objWriter->writeAttribute('name', $drawing->getName());
            $objWriter->writeAttribute('descr', $drawing->getDescription());

            //a:hlinkClick
            $this->writeHyperLinkDrawing($objWriter, $hlinkClickId);

            $objWriter->endElement();

            // xdr:cNvPicPr
            $objWriter->startElement('xdr:cNvPicPr');

            // a:picLocks
            $objWriter->startElement('a:picLocks');
            $objWriter->writeAttribute('noChangeAspect', '1');
            $objWriter->endElement();

            $objWriter->endElement();

            $objWriter->endElement();

            // xdr:blipFill
            $objWriter->startElement('xdr:blipFill');

            // a:blip
            $objWriter->startElement('a:blip');
            $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
            $objWriter->writeAttribute('r:embed', 'rId' . $relationId);
            $temp = $drawing->getOpacity();
            if (is_int($temp) && $temp >= 0 && $temp <= 100000) {
                $objWriter->startElement('a:alphaModFix');
                $objWriter->writeAttribute('amt', "$temp");
                $objWriter->endElement(); // a:alphaModFix
            }
            $objWriter->endElement(); // a:blip

            $srcRect = $drawing->getSrcRect();
            if (!empty($srcRect)) {
                $objWriter->startElement('a:srcRect');
                foreach ($srcRect as $key => $value) {
                    $objWriter->writeAttribute($key, (string) $value);
                }
                $objWriter->endElement(); // a:srcRect
                $objWriter->startElement('a:stretch');
                $objWriter->endElement(); // a:stretch
            } else {
                // a:stretch
                $objWriter->startElement('a:stretch');
                $objWriter->writeElement('a:fillRect', null);
                $objWriter->endElement();
            }

            $objWriter->endElement();

            // xdr:spPr
            $objWriter->startElement('xdr:spPr');

            // a:xfrm
            $objWriter->startElement('a:xfrm');
            $objWriter->writeAttribute('rot', (string) SharedDrawing::degreesToAngle($drawing->getRotation()));
            self::writeAttributeIf($objWriter, $drawing->getFlipVertical(), 'flipV', '1');
            self::writeAttributeIf($objWriter, $drawing->getFlipHorizontal(), 'flipH', '1');
            if ($isTwoCellAnchor) {
                $objWriter->startElement('a:ext');
                $objWriter->writeAttribute('cx', self::stringEmu($drawing->getWidth()));
                $objWriter->writeAttribute('cy', self::stringEmu($drawing->getHeight()));
                $objWriter->endElement();
            }
            $objWriter->endElement();

            // a:prstGeom
            $objWriter->startElement('a:prstGeom');
            $objWriter->writeAttribute('prst', 'rect');

            // a:avLst
            $objWriter->writeElement('a:avLst', null);

            $objWriter->endElement();

            if ($drawing->getShadow()->getVisible()) {
                // a:effectLst
                $objWriter->startElement('a:effectLst');

                // a:outerShdw
                $objWriter->startElement('a:outerShdw');
                $objWriter->writeAttribute('blurRad', self::stringEmu($drawing->getShadow()->getBlurRadius()));
                $objWriter->writeAttribute('dist', self::stringEmu($drawing->getShadow()->getDistance()));
                $objWriter->writeAttribute('dir', (string) SharedDrawing::degreesToAngle($drawing->getShadow()->getDirection()));
                $objWriter->writeAttribute('algn', $drawing->getShadow()->getAlignment());
                $objWriter->writeAttribute('rotWithShape', '0');

                // a:srgbClr
                $objWriter->startElement('a:srgbClr');
                $objWriter->writeAttribute('val', $drawing->getShadow()->getColor()->getRGB());

                // a:alpha
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', (string) ($drawing->getShadow()->getAlpha() * 1000));
                $objWriter->endElement();

                $objWriter->endElement();

                $objWriter->endElement();

                $objWriter->endElement();
            }
            $objWriter->endElement();

            $objWriter->endElement();

            // xdr:clientData
            $objWriter->writeElement('xdr:clientData', null);

            $objWriter->endElement();
        } else {
            throw new WriterException('Invalid parameters passed.');
        }
    }

    /**
     * Write VML header/footer images to XML format.
     *
     * @return string XML Output
     */
    public function writeVMLHeaderFooterImages(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): string
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

        // Header/footer images
        $images = $worksheet->getHeaderFooter()->getImages();

        // xml
        $objWriter->startElement('xml');
        $objWriter->writeAttribute('xmlns:v', Namespaces::URN_VML);
        $objWriter->writeAttribute('xmlns:o', Namespaces::URN_MSOFFICE);
        $objWriter->writeAttribute('xmlns:x', Namespaces::URN_EXCEL);

        // o:shapelayout
        $objWriter->startElement('o:shapelayout');
        $objWriter->writeAttribute('v:ext', 'edit');

        // o:idmap
        $objWriter->startElement('o:idmap');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('data', '1');
        $objWriter->endElement();

        $objWriter->endElement();

        // v:shapetype
        $objWriter->startElement('v:shapetype');
        $objWriter->writeAttribute('id', '_x0000_t75');
        $objWriter->writeAttribute('coordsize', '21600,21600');
        $objWriter->writeAttribute('o:spt', '75');
        $objWriter->writeAttribute('o:preferrelative', 't');
        $objWriter->writeAttribute('path', 'm@4@5l@4@11@9@11@9@5xe');
        $objWriter->writeAttribute('filled', 'f');
        $objWriter->writeAttribute('stroked', 'f');

        // v:stroke
        $objWriter->startElement('v:stroke');
        $objWriter->writeAttribute('joinstyle', 'miter');
        $objWriter->endElement();

        // v:formulas
        $objWriter->startElement('v:formulas');

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'if lineDrawn pixelLineWidth 0');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @0 1 0');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum 0 0 @1');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @2 1 2');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @3 21600 pixelWidth');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @3 21600 pixelHeight');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @0 0 1');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @6 1 2');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @7 21600 pixelWidth');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @8 21600 0');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'prod @7 21600 pixelHeight');
        $objWriter->endElement();

        // v:f
        $objWriter->startElement('v:f');
        $objWriter->writeAttribute('eqn', 'sum @10 21600 0');
        $objWriter->endElement();

        $objWriter->endElement();

        // v:path
        $objWriter->startElement('v:path');
        $objWriter->writeAttribute('o:extrusionok', 'f');
        $objWriter->writeAttribute('gradientshapeok', 't');
        $objWriter->writeAttribute('o:connecttype', 'rect');
        $objWriter->endElement();

        // o:lock
        $objWriter->startElement('o:lock');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('aspectratio', 't');
        $objWriter->endElement();

        $objWriter->endElement();

        // Loop through images
        foreach ($images as $key => $value) {
            $this->writeVMLHeaderFooterImage($objWriter, $key, $value);
        }

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write VML comment to XML format.
     *
     * @param string $reference Reference
     */
    private function writeVMLHeaderFooterImage(XMLWriter $objWriter, string $reference, HeaderFooterDrawing $image): void
    {
        // Calculate object id
        if (preg_match('{(\d+)}', md5($reference), $m) !== 1) {
            // @codeCoverageIgnoreStart
            throw new WriterException('Regexp failure in writeVMLHeaderFooterImage');
            // @codeCoverageIgnoreEnd
        }
        $id = 1500 + ((int) substr($m[1], 0, 2) * 1);

        // Calculate offset
        $width = $image->getWidth();
        $height = $image->getHeight();
        $marginLeft = $image->getOffsetX();
        $marginTop = $image->getOffsetY();

        // v:shape
        $objWriter->startElement('v:shape');
        $objWriter->writeAttribute('id', $reference);
        $objWriter->writeAttribute('o:spid', '_x0000_s' . $id);
        $objWriter->writeAttribute('type', '#_x0000_t75');
        $objWriter->writeAttribute('style', "position:absolute;margin-left:{$marginLeft}px;margin-top:{$marginTop}px;width:{$width}px;height:{$height}px;z-index:1");

        // v:imagedata
        $objWriter->startElement('v:imagedata');
        $objWriter->writeAttribute('o:relid', 'rId' . $reference);
        $objWriter->writeAttribute('o:title', $image->getName());
        $objWriter->endElement();

        // o:lock
        $objWriter->startElement('o:lock');
        $objWriter->writeAttribute('v:ext', 'edit');
        $objWriter->writeAttribute('textRotation', 't');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Get an array of all drawings.
     *
     * @return BaseDrawing[] All drawings in PhpSpreadsheet
     */
    public function allDrawings(Spreadsheet $spreadsheet): array
    {
        // Get an array of all drawings
        $aDrawings = [];

        // Loop through PhpSpreadsheet
        $sheetCount = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            // Loop through images and add to array
            $iterator = $spreadsheet->getSheet($i)->getDrawingCollection()->getIterator();
            while ($iterator->valid()) {
                $aDrawings[] = $iterator->current();

                $iterator->next();
            }
        }

        return $aDrawings;
    }

    private function writeHyperLinkDrawing(XMLWriter $objWriter, ?int $hlinkClickId): void
    {
        if ($hlinkClickId === null) {
            return;
        }

        $objWriter->startElement('a:hlinkClick');
        $objWriter->writeAttribute('xmlns:r', Namespaces::SCHEMA_OFFICE_DOCUMENT);
        $objWriter->writeAttribute('r:id', 'rId' . $hlinkClickId);
        $objWriter->endElement();
    }

    private static function stringEmu(int $pixelValue): string
    {
        return (string) SharedDrawing::pixelsToEMU($pixelValue);
    }

    private static function writeAttributeIf(XMLWriter $objWriter, ?bool $condition, string $attr, string $val): void
    {
        if ($condition) {
            $objWriter->writeAttribute($attr, $val);
        }
    }
}

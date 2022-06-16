<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

class Chart extends WriterPart
{
    protected $calculateCellValues;

    /**
     * @var int
     */
    private $seriesIndex;

    /**
     * Write charts to XML format.
     *
     * @param mixed $calculateCellValues
     *
     * @return string XML Output
     */
    public function writeChart(\PhpOffice\PhpSpreadsheet\Chart\Chart $chart, $calculateCellValues = true)
    {
        $this->calculateCellValues = $calculateCellValues;

        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new XMLWriter(XMLWriter::STORAGE_MEMORY);
        }
        //    Ensure that data series values are up-to-date before we save
        if ($this->calculateCellValues) {
            $chart->refresh();
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // c:chartSpace
        $objWriter->startElement('c:chartSpace');
        $objWriter->writeAttribute('xmlns:c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
        $objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $objWriter->startElement('c:date1904');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();
        $objWriter->startElement('c:lang');
        $objWriter->writeAttribute('val', 'en-GB');
        $objWriter->endElement();
        $objWriter->startElement('c:roundedCorners');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $this->writeAlternateContent($objWriter);

        $objWriter->startElement('c:chart');

        $this->writeTitle($objWriter, $chart->getTitle());

        $objWriter->startElement('c:autoTitleDeleted');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:view3D');
        $rotX = $chart->getRotX();
        if (is_int($rotX)) {
            $objWriter->startElement('c:rotX');
            $objWriter->writeAttribute('val', "$rotX");
            $objWriter->endElement();
        }
        $rotY = $chart->getRotY();
        if (is_int($rotY)) {
            $objWriter->startElement('c:rotY');
            $objWriter->writeAttribute('val', "$rotY");
            $objWriter->endElement();
        }
        $rAngAx = $chart->getRAngAx();
        if (is_int($rAngAx)) {
            $objWriter->startElement('c:rAngAx');
            $objWriter->writeAttribute('val', "$rAngAx");
            $objWriter->endElement();
        }
        $perspective = $chart->getPerspective();
        if (is_int($perspective)) {
            $objWriter->startElement('c:perspective');
            $objWriter->writeAttribute('val', "$perspective");
            $objWriter->endElement();
        }
        $objWriter->endElement(); // view3D

        $this->writePlotArea($objWriter, $chart->getPlotArea(), $chart->getXAxisLabel(), $chart->getYAxisLabel(), $chart->getChartAxisX(), $chart->getChartAxisY(), $chart->getMajorGridlines(), $chart->getMinorGridlines());

        $this->writeLegend($objWriter, $chart->getLegend());

        $objWriter->startElement('c:plotVisOnly');
        $objWriter->writeAttribute('val', (int) $chart->getPlotVisibleOnly());
        $objWriter->endElement();

        $objWriter->startElement('c:dispBlanksAs');
        $objWriter->writeAttribute('val', $chart->getDisplayBlanksAs());
        $objWriter->endElement();

        $objWriter->startElement('c:showDLblsOverMax');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->endElement();

        $this->writePrintSettings($objWriter);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write Chart Title.
     */
    private function writeTitle(XMLWriter $objWriter, ?Title $title = null): void
    {
        if ($title === null) {
            return;
        }

        $objWriter->startElement('c:title');
        $objWriter->startElement('c:tx');
        $objWriter->startElement('c:rich');

        $objWriter->startElement('a:bodyPr');
        $objWriter->endElement();

        $objWriter->startElement('a:lstStyle');
        $objWriter->endElement();

        $objWriter->startElement('a:p');
        $objWriter->startElement('a:pPr');
        $objWriter->startElement('a:defRPr');
        $objWriter->endElement();
        $objWriter->endElement();

        $caption = $title->getCaption();
        if ((is_array($caption)) && (count($caption) > 0)) {
            $caption = $caption[0];
        }
        $this->getParentWriter()->getWriterPartstringtable()->writeRichTextForCharts($objWriter, $caption, 'a');

        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        $this->writeLayout($objWriter, $title->getLayout());

        $objWriter->startElement('c:overlay');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Chart Legend.
     */
    private function writeLegend(XMLWriter $objWriter, ?Legend $legend = null): void
    {
        if ($legend === null) {
            return;
        }

        $objWriter->startElement('c:legend');

        $objWriter->startElement('c:legendPos');
        $objWriter->writeAttribute('val', $legend->getPosition());
        $objWriter->endElement();

        $this->writeLayout($objWriter, $legend->getLayout());

        $objWriter->startElement('c:overlay');
        $objWriter->writeAttribute('val', ($legend->getOverlay()) ? '1' : '0');
        $objWriter->endElement();

        $objWriter->startElement('c:txPr');
        $objWriter->startElement('a:bodyPr');
        $objWriter->endElement();

        $objWriter->startElement('a:lstStyle');
        $objWriter->endElement();

        $objWriter->startElement('a:p');
        $objWriter->startElement('a:pPr');
        $objWriter->writeAttribute('rtl', 0);

        $objWriter->startElement('a:defRPr');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('a:endParaRPr');
        $objWriter->writeAttribute('lang', 'en-US');
        $objWriter->endElement();

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Chart Plot Area.
     */
    private function writePlotArea(XMLWriter $objWriter, PlotArea $plotArea, ?Title $xAxisLabel = null, ?Title $yAxisLabel = null, ?Axis $xAxis = null, ?Axis $yAxis = null, ?GridLines $majorGridlines = null, ?GridLines $minorGridlines = null): void
    {
        if ($plotArea === null) {
            return;
        }

        $id1 = $id2 = $id3 = '0';
        $this->seriesIndex = 0;
        $objWriter->startElement('c:plotArea');

        $layout = $plotArea->getLayout();

        $this->writeLayout($objWriter, $layout);

        $chartTypes = self::getChartType($plotArea);
        $catIsMultiLevelSeries = $valIsMultiLevelSeries = false;
        $plotGroupingType = '';
        $chartType = null;
        foreach ($chartTypes as $chartType) {
            $objWriter->startElement('c:' . $chartType);

            $groupCount = $plotArea->getPlotGroupCount();
            $plotGroup = null;
            for ($i = 0; $i < $groupCount; ++$i) {
                $plotGroup = $plotArea->getPlotGroupByIndex($i);
                $groupType = $plotGroup->getPlotType();
                if ($groupType == $chartType) {
                    $plotStyle = $plotGroup->getPlotStyle();
                    if (!empty($plotStyle) && $groupType === DataSeries::TYPE_RADARCHART) {
                        $objWriter->startElement('c:radarStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    } elseif (!empty($plotStyle) && $groupType === DataSeries::TYPE_SCATTERCHART) {
                        $objWriter->startElement('c:scatterStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    } elseif ($groupType === DataSeries::TYPE_SURFACECHART_3D || $groupType === DataSeries::TYPE_SURFACECHART) {
                        $objWriter->startElement('c:wireframe');
                        $objWriter->writeAttribute('val', $plotStyle ? '1' : '0');
                        $objWriter->endElement();
                    }

                    $this->writePlotGroup($plotGroup, $chartType, $objWriter, $catIsMultiLevelSeries, $valIsMultiLevelSeries, $plotGroupingType);
                }
            }

            $this->writeDataLabels($objWriter, $layout);

            if ($chartType === DataSeries::TYPE_LINECHART && $plotGroup) {
                //    Line only, Line3D can't be smoothed
                $objWriter->startElement('c:smooth');
                $objWriter->writeAttribute('val', (int) $plotGroup->getSmoothLine());
                $objWriter->endElement();
            } elseif (($chartType === DataSeries::TYPE_BARCHART) || ($chartType === DataSeries::TYPE_BARCHART_3D)) {
                $objWriter->startElement('c:gapWidth');
                $objWriter->writeAttribute('val', 150);
                $objWriter->endElement();

                if ($plotGroupingType == 'percentStacked' || $plotGroupingType == 'stacked') {
                    $objWriter->startElement('c:overlap');
                    $objWriter->writeAttribute('val', 100);
                    $objWriter->endElement();
                }
            } elseif ($chartType === DataSeries::TYPE_BUBBLECHART) {
                $scale = ($plotGroup === null) ? '' : (string) $plotGroup->getPlotStyle();
                if ($scale !== '') {
                    $objWriter->startElement('c:bubbleScale');
                    $objWriter->writeAttribute('val', $scale);
                    $objWriter->endElement();
                }

                $objWriter->startElement('c:showNegBubbles');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            } elseif ($chartType === DataSeries::TYPE_STOCKCHART) {
                $objWriter->startElement('c:hiLowLines');
                $objWriter->endElement();

                $objWriter->startElement('c:upDownBars');

                $objWriter->startElement('c:gapWidth');
                $objWriter->writeAttribute('val', 300);
                $objWriter->endElement();

                $objWriter->startElement('c:upBars');
                $objWriter->endElement();

                $objWriter->startElement('c:downBars');
                $objWriter->endElement();

                $objWriter->endElement();
            }

            //    Generate 3 unique numbers to use for axId values
            $id1 = '110438656';
            $id2 = '110444544';
            $id3 = '110365312'; // used in Surface Chart

            if (($chartType !== DataSeries::TYPE_PIECHART) && ($chartType !== DataSeries::TYPE_PIECHART_3D) && ($chartType !== DataSeries::TYPE_DONUTCHART)) {
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id1);
                $objWriter->endElement();
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id2);
                $objWriter->endElement();
                if ($chartType === DataSeries::TYPE_SURFACECHART_3D || $chartType === DataSeries::TYPE_SURFACECHART) {
                    $objWriter->startElement('c:axId');
                    $objWriter->writeAttribute('val', $id3);
                    $objWriter->endElement();
                }
            } else {
                $objWriter->startElement('c:firstSliceAng');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();

                if ($chartType === DataSeries::TYPE_DONUTCHART) {
                    $objWriter->startElement('c:holeSize');
                    $objWriter->writeAttribute('val', 50);
                    $objWriter->endElement();
                }
            }

            $objWriter->endElement();
        }

        if (($chartType !== DataSeries::TYPE_PIECHART) && ($chartType !== DataSeries::TYPE_PIECHART_3D) && ($chartType !== DataSeries::TYPE_DONUTCHART)) {
            if ($chartType === DataSeries::TYPE_BUBBLECHART) {
                $this->writeValueAxis($objWriter, $xAxisLabel, $chartType, $id2, $id1, $catIsMultiLevelSeries, $xAxis, $majorGridlines, $minorGridlines);
            } else {
                $this->writeCategoryAxis($objWriter, $xAxisLabel, $id1, $id2, $catIsMultiLevelSeries, $xAxis);
            }

            $this->writeValueAxis($objWriter, $yAxisLabel, $chartType, $id1, $id2, $valIsMultiLevelSeries, $yAxis, $majorGridlines, $minorGridlines);
            if ($chartType === DataSeries::TYPE_SURFACECHART_3D || $chartType === DataSeries::TYPE_SURFACECHART) {
                $this->writeSerAxis($objWriter, $id2, $id3);
            }
        }

        $objWriter->endElement();
    }

    /**
     * Write Data Labels.
     */
    private function writeDataLabels(XMLWriter $objWriter, ?Layout $chartLayout = null): void
    {
        $objWriter->startElement('c:dLbls');

        $objWriter->startElement('c:showLegendKey');
        $showLegendKey = (empty($chartLayout)) ? 0 : $chartLayout->getShowLegendKey();
        $objWriter->writeAttribute('val', ((empty($showLegendKey)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showVal');
        $showVal = (empty($chartLayout)) ? 0 : $chartLayout->getShowVal();
        $objWriter->writeAttribute('val', ((empty($showVal)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showCatName');
        $showCatName = (empty($chartLayout)) ? 0 : $chartLayout->getShowCatName();
        $objWriter->writeAttribute('val', ((empty($showCatName)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showSerName');
        $showSerName = (empty($chartLayout)) ? 0 : $chartLayout->getShowSerName();
        $objWriter->writeAttribute('val', ((empty($showSerName)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showPercent');
        $showPercent = (empty($chartLayout)) ? 0 : $chartLayout->getShowPercent();
        $objWriter->writeAttribute('val', ((empty($showPercent)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showBubbleSize');
        $showBubbleSize = (empty($chartLayout)) ? 0 : $chartLayout->getShowBubbleSize();
        $objWriter->writeAttribute('val', ((empty($showBubbleSize)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->startElement('c:showLeaderLines');
        $showLeaderLines = (empty($chartLayout)) ? 1 : $chartLayout->getShowLeaderLines();
        $objWriter->writeAttribute('val', ((empty($showLeaderLines)) ? 0 : 1));
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Category Axis.
     *
     * @param string $id1
     * @param string $id2
     * @param bool $isMultiLevelSeries
     */
    private function writeCategoryAxis(XMLWriter $objWriter, ?Title $xAxisLabel, $id1, $id2, $isMultiLevelSeries, Axis $yAxis): void
    {
        // N.B. writeCategoryAxis may be invoked with the last parameter($yAxis) using $xAxis for ScatterChart, etc
        // In that case, xAxis is NOT a category.
        if ($yAxis->getAxisIsNumericFormat()) {
            $objWriter->startElement('c:valAx');
        } else {
            $objWriter->startElement('c:catAx');
        }

        if ($id1 !== '0') {
            $objWriter->startElement('c:axId');
            $objWriter->writeAttribute('val', $id1);
            $objWriter->endElement();
        }

        $objWriter->startElement('c:scaling');
        if ($yAxis->getAxisOptionsProperty('maximum') !== null) {
            $objWriter->startElement('c:max');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('maximum'));
            $objWriter->endElement();
        }
        if ($yAxis->getAxisOptionsProperty('minimum') !== null) {
            $objWriter->startElement('c:min');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('minimum'));
            $objWriter->endElement();
        }
        if (!empty($yAxis->getAxisOptionsProperty('orientation'))) {
            $objWriter->startElement('c:orientation');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('orientation'));
            $objWriter->endElement();
        }
        $objWriter->endElement(); // c:scaling

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', 'b');
        $objWriter->endElement();

        if ($xAxisLabel !== null) {
            $objWriter->startElement('c:title');
            $objWriter->startElement('c:tx');
            $objWriter->startElement('c:rich');

            $objWriter->startElement('a:bodyPr');
            $objWriter->endElement();

            $objWriter->startElement('a:lstStyle');
            $objWriter->endElement();

            $objWriter->startElement('a:p');
            $objWriter->startElement('a:pPr');
            $objWriter->startElement('a:defRPr');
            $objWriter->endElement();
            $objWriter->endElement();

            $caption = $xAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }
            $this->getParentWriter()->getWriterPartstringtable()->writeRichTextForCharts($objWriter, $caption, 'a');

            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();

            $layout = $xAxisLabel->getLayout();
            $this->writeLayout($objWriter, $layout);

            $objWriter->startElement('c:overlay');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();

            $objWriter->endElement();
        }

        $objWriter->startElement('c:numFmt');
        $objWriter->writeAttribute('formatCode', $yAxis->getAxisNumberFormat());
        $objWriter->writeAttribute('sourceLinked', $yAxis->getAxisNumberSourceLinked());
        $objWriter->endElement();

        if (!empty($yAxis->getAxisOptionsProperty('major_tick_mark'))) {
            $objWriter->startElement('c:majorTickMark');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('major_tick_mark'));
            $objWriter->endElement();
        }

        if (!empty($yAxis->getAxisOptionsProperty('minor_tick_mark'))) {
            $objWriter->startElement('c:minorTickMark');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('minor_tick_mark'));
            $objWriter->endElement();
        }

        if (!empty($yAxis->getAxisOptionsProperty('axis_labels'))) {
            $objWriter->startElement('c:tickLblPos');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('axis_labels'));
            $objWriter->endElement();
        }

        $objWriter->startElement('c:spPr');
        if (!empty($yAxis->getFillProperty('value'))) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement('a:' . $yAxis->getFillProperty('type'));
            $objWriter->writeAttribute('val', $yAxis->getFillProperty('value'));
            $alpha = $yAxis->getFillProperty('alpha');
            if (is_numeric($alpha)) {
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', Properties::alphaToXml((int) $alpha));
                $objWriter->endElement();
            }
            $objWriter->endElement();
            $objWriter->endElement();
        }
        $objWriter->startElement('a:effectLst');
        $this->writeGlow($objWriter, $yAxis);
        $this->writeShadow($objWriter, $yAxis);
        $this->writeSoftEdge($objWriter, $yAxis);
        $objWriter->endElement(); // effectLst
        $objWriter->endElement(); // spPr

        if ($yAxis->getAxisOptionsProperty('major_unit') !== null) {
            $objWriter->startElement('c:majorUnit');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('major_unit'));
            $objWriter->endElement();
        }

        if ($yAxis->getAxisOptionsProperty('minor_unit') !== null) {
            $objWriter->startElement('c:minorUnit');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('minor_unit'));
            $objWriter->endElement();
        }

        if ($id2 !== '0') {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();

            if (!empty($yAxis->getAxisOptionsProperty('horizontal_crosses'))) {
                $objWriter->startElement('c:crosses');
                $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('horizontal_crosses'));
                $objWriter->endElement();
            }
        }

        $objWriter->startElement('c:auto');
        $objWriter->writeAttribute('val', 1);
        $objWriter->endElement();

        $objWriter->startElement('c:lblAlgn');
        $objWriter->writeAttribute('val', 'ctr');
        $objWriter->endElement();

        $objWriter->startElement('c:lblOffset');
        $objWriter->writeAttribute('val', 100);
        $objWriter->endElement();

        if ($isMultiLevelSeries) {
            $objWriter->startElement('c:noMultiLvlLbl');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    /**
     * Write Value Axis.
     *
     * @param null|string $groupType Chart type
     * @param string $id1
     * @param string $id2
     * @param bool $isMultiLevelSeries
     */
    private function writeValueAxis(XMLWriter $objWriter, ?Title $yAxisLabel, $groupType, $id1, $id2, $isMultiLevelSeries, Axis $xAxis, GridLines $majorGridlines, GridLines $minorGridlines): void
    {
        $objWriter->startElement('c:valAx');

        if ($id2 !== '0') {
            $objWriter->startElement('c:axId');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();
        }

        $objWriter->startElement('c:scaling');

        if ($xAxis->getAxisOptionsProperty('maximum') !== null) {
            $objWriter->startElement('c:max');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('maximum'));
            $objWriter->endElement();
        }

        if ($xAxis->getAxisOptionsProperty('minimum') !== null) {
            $objWriter->startElement('c:min');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minimum'));
            $objWriter->endElement();
        }

        if (!empty($xAxis->getAxisOptionsProperty('orientation'))) {
            $objWriter->startElement('c:orientation');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('orientation'));
            $objWriter->endElement();
        }

        $objWriter->endElement(); // c:scaling

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', 'l');
        $objWriter->endElement();

        $objWriter->startElement('c:majorGridlines');
        $objWriter->startElement('c:spPr');

        $this->writeGridlinesLn($objWriter, $majorGridlines);

        $objWriter->startElement('a:effectLst');
        $this->writeGlow($objWriter, $majorGridlines);
        $this->writeShadow($objWriter, $majorGridlines);
        $this->writeSoftEdge($objWriter, $majorGridlines);

        $objWriter->endElement(); //end effectLst
        $objWriter->endElement(); //end spPr
        $objWriter->endElement(); //end majorGridLines

        if ($minorGridlines->getObjectState()) {
            $objWriter->startElement('c:minorGridlines');
            $objWriter->startElement('c:spPr');

            $this->writeGridlinesLn($objWriter, $minorGridlines);

            $objWriter->startElement('a:effectLst');
            $this->writeGlow($objWriter, $minorGridlines);
            $this->writeShadow($objWriter, $minorGridlines);
            $this->writeSoftEdge($objWriter, $minorGridlines);
            $objWriter->endElement(); //end effectLst

            $objWriter->endElement(); //end spPr
            $objWriter->endElement(); //end minorGridLines
        }

        if ($yAxisLabel !== null) {
            $objWriter->startElement('c:title');
            $objWriter->startElement('c:tx');
            $objWriter->startElement('c:rich');

            $objWriter->startElement('a:bodyPr');
            $objWriter->endElement();

            $objWriter->startElement('a:lstStyle');
            $objWriter->endElement();

            $objWriter->startElement('a:p');
            $objWriter->startElement('a:pPr');
            $objWriter->startElement('a:defRPr');
            $objWriter->endElement();
            $objWriter->endElement();

            $caption = $yAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }
            $this->getParentWriter()->getWriterPartstringtable()->writeRichTextForCharts($objWriter, $caption, 'a');

            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();

            if ($groupType !== DataSeries::TYPE_BUBBLECHART) {
                $layout = $yAxisLabel->getLayout();
                $this->writeLayout($objWriter, $layout);
            }

            $objWriter->startElement('c:overlay');
            $objWriter->writeAttribute('val', 0);
            $objWriter->endElement();

            $objWriter->endElement();
        }

        $objWriter->startElement('c:numFmt');
        $objWriter->writeAttribute('formatCode', $xAxis->getAxisNumberFormat());
        $objWriter->writeAttribute('sourceLinked', $xAxis->getAxisNumberSourceLinked());
        $objWriter->endElement();

        if (!empty($xAxis->getAxisOptionsProperty('major_tick_mark'))) {
            $objWriter->startElement('c:majorTickMark');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('major_tick_mark'));
            $objWriter->endElement();
        }

        if (!empty($xAxis->getAxisOptionsProperty('minor_tick_mark'))) {
            $objWriter->startElement('c:minorTickMark');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minor_tick_mark'));
            $objWriter->endElement();
        }

        if (!empty($xAxis->getAxisOptionsProperty('axis_labels'))) {
            $objWriter->startElement('c:tickLblPos');
            $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('axis_labels'));
            $objWriter->endElement();
        }

        $objWriter->startElement('c:spPr');

        if (!empty($xAxis->getFillProperty('value'))) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement('a:' . $xAxis->getFillProperty('type'));
            $objWriter->writeAttribute('val', $xAxis->getFillProperty('value'));
            $alpha = $xAxis->getFillProperty('alpha');
            if (is_numeric($alpha)) {
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', Properties::alphaToXml((int) $alpha));
                $objWriter->endElement();
            }
            $objWriter->endElement();
            $objWriter->endElement();
        }

        $this->writeGridlinesLn($objWriter, $xAxis);

        $objWriter->startElement('a:effectLst');
        $this->writeGlow($objWriter, $xAxis);
        $this->writeShadow($objWriter, $xAxis);
        $this->writeSoftEdge($objWriter, $xAxis);
        $objWriter->endElement(); //effectList

        $objWriter->endElement(); //end spPr

        if ($id1 !== '0') {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id1);
            $objWriter->endElement();

            if ($xAxis->getAxisOptionsProperty('horizontal_crosses_value') !== null) {
                $objWriter->startElement('c:crossesAt');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('horizontal_crosses_value'));
                $objWriter->endElement();
            } else {
                $crosses = $xAxis->getAxisOptionsProperty('horizontal_crosses');
                if ($crosses) {
                    $objWriter->startElement('c:crosses');
                    $objWriter->writeAttribute('val', $crosses);
                    $objWriter->endElement();
                }
            }

            $crossBetween = $xAxis->getCrossBetween();
            if ($crossBetween !== '') {
                $objWriter->startElement('c:crossBetween');
                $objWriter->writeAttribute('val', $crossBetween);
                $objWriter->endElement();
            }

            if ($xAxis->getAxisOptionsProperty('major_unit') !== null) {
                $objWriter->startElement('c:majorUnit');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('major_unit'));
                $objWriter->endElement();
            }

            if ($xAxis->getAxisOptionsProperty('minor_unit') !== null) {
                $objWriter->startElement('c:minorUnit');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minor_unit'));
                $objWriter->endElement();
            }
        }

        if ($isMultiLevelSeries) {
            if ($groupType !== DataSeries::TYPE_BUBBLECHART) {
                $objWriter->startElement('c:noMultiLvlLbl');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            }
        }

        $objWriter->endElement();
    }

    /**
     * Write Ser Axis, for Surface chart.
     */
    private function writeSerAxis(XMLWriter $objWriter, string $id2, string $id3): void
    {
        $objWriter->startElement('c:serAx');

        $objWriter->startElement('c:axId');
        $objWriter->writeAttribute('val', $id3);
        $objWriter->endElement(); // axId

        $objWriter->startElement('c:scaling');
        $objWriter->startElement('c:orientation');
        $objWriter->writeAttribute('val', 'minMax');
        $objWriter->endElement(); // orientation
        $objWriter->endElement(); // scaling

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', '0');
        $objWriter->endElement(); // delete

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', 'b');
        $objWriter->endElement(); // axPos

        $objWriter->startElement('c:majorTickMark');
        $objWriter->writeAttribute('val', 'out');
        $objWriter->endElement(); // majorTickMark

        $objWriter->startElement('c:minorTickMark');
        $objWriter->writeAttribute('val', 'none');
        $objWriter->endElement(); // minorTickMark

        $objWriter->startElement('c:tickLblPos');
        $objWriter->writeAttribute('val', 'nextTo');
        $objWriter->endElement(); // tickLblPos

        $objWriter->startElement('c:crossAx');
        $objWriter->writeAttribute('val', $id2);
        $objWriter->endElement(); // crossAx

        $objWriter->startElement('c:crosses');
        $objWriter->writeAttribute('val', 'autoZero');
        $objWriter->endElement(); // crosses

        $objWriter->endElement(); //serAx
    }

    /**
     * Get the data series type(s) for a chart plot series.
     *
     * @return string[]
     */
    private static function getChartType(PlotArea $plotArea): array
    {
        $groupCount = $plotArea->getPlotGroupCount();

        if ($groupCount == 1) {
            $chartType = [$plotArea->getPlotGroupByIndex(0)->getPlotType()];
        } else {
            $chartTypes = [];
            for ($i = 0; $i < $groupCount; ++$i) {
                $chartTypes[] = $plotArea->getPlotGroupByIndex($i)->getPlotType();
            }
            $chartType = array_unique($chartTypes);
            if (count($chartTypes) == 0) {
                throw new WriterException('Chart is not yet implemented');
            }
        }

        return $chartType;
    }

    /**
     * Method writing plot series values.
     *
     * @param int $val value for idx (default: 3)
     * @param string $fillColor hex color (default: FF9900)
     */
    private function writePlotSeriesValuesElement(XMLWriter $objWriter, $val = 3, $fillColor = 'FF9900'): void
    {
        if ($fillColor === '') {
            return;
        }
        $objWriter->startElement('c:dPt');
        $objWriter->startElement('c:idx');
        $objWriter->writeAttribute('val', $val);
        $objWriter->endElement();

        $objWriter->startElement('c:bubble3D');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:spPr');
        $objWriter->startElement('a:solidFill');
        if (substr($fillColor, 0, 1) === '*') {
            $objWriter->startElement('a:schemeClr');
            $objWriter->writeAttribute('val', substr($fillColor, 1));
        } elseif (substr($fillColor, 0, 1) === '/') {
            $objWriter->startElement('a:prstClr');
            $objWriter->writeAttribute('val', substr($fillColor, 1));
        } else {
            $objWriter->startElement('a:srgbClr');
            $objWriter->writeAttribute('val', $fillColor);
        }
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();
    }

    /**
     * Write Plot Group (series of related plots).
     *
     * @param string $groupType Type of plot for dataseries
     * @param bool $catIsMultiLevelSeries Is category a multi-series category
     * @param bool $valIsMultiLevelSeries Is value set a multi-series set
     * @param string $plotGroupingType Type of grouping for multi-series values
     */
    private function writePlotGroup(?DataSeries $plotGroup, string $groupType, XMLWriter $objWriter, &$catIsMultiLevelSeries, &$valIsMultiLevelSeries, &$plotGroupingType): void
    {
        if ($plotGroup === null) {
            return;
        }

        if (($groupType == DataSeries::TYPE_BARCHART) || ($groupType == DataSeries::TYPE_BARCHART_3D)) {
            $objWriter->startElement('c:barDir');
            $objWriter->writeAttribute('val', $plotGroup->getPlotDirection());
            $objWriter->endElement();
        }

        if ($plotGroup->getPlotGrouping() !== null) {
            $plotGroupingType = $plotGroup->getPlotGrouping();
            $objWriter->startElement('c:grouping');
            $objWriter->writeAttribute('val', $plotGroupingType);
            $objWriter->endElement();
        }

        //    Get these details before the loop, because we can use the count to check for varyColors
        $plotSeriesOrder = $plotGroup->getPlotOrder();
        $plotSeriesCount = count($plotSeriesOrder);

        if (($groupType !== DataSeries::TYPE_RADARCHART) && ($groupType !== DataSeries::TYPE_STOCKCHART)) {
            if ($groupType !== DataSeries::TYPE_LINECHART) {
                if (($groupType == DataSeries::TYPE_PIECHART) || ($groupType == DataSeries::TYPE_PIECHART_3D) || ($groupType == DataSeries::TYPE_DONUTCHART) || ($plotSeriesCount > 1)) {
                    $objWriter->startElement('c:varyColors');
                    $objWriter->writeAttribute('val', 1);
                    $objWriter->endElement();
                } else {
                    $objWriter->startElement('c:varyColors');
                    $objWriter->writeAttribute('val', 0);
                    $objWriter->endElement();
                }
            }
        }

        $plotSeriesIdx = 0;
        foreach ($plotSeriesOrder as $plotSeriesIdx => $plotSeriesRef) {
            $objWriter->startElement('c:ser');

            $plotLabel = $plotGroup->getPlotLabelByIndex($plotSeriesIdx);
            if ($plotLabel && $groupType !== DataSeries::TYPE_LINECHART) {
                $fillColor = $plotLabel->getFillColor();
                if ($fillColor !== null && !is_array($fillColor)) {
                    $objWriter->startElement('c:spPr');
                    $objWriter->startElement('a:solidFill');
                    $objWriter->startElement('a:srgbClr');
                    $objWriter->writeAttribute('val', $fillColor);
                    $objWriter->endElement();
                    $objWriter->endElement();
                    $objWriter->endElement();
                }
            }

            $objWriter->startElement('c:idx');
            $objWriter->writeAttribute('val', $this->seriesIndex + $plotSeriesIdx);
            $objWriter->endElement();

            $objWriter->startElement('c:order');
            $objWriter->writeAttribute('val', $this->seriesIndex + $plotSeriesRef);
            $objWriter->endElement();

            //    Values
            $plotSeriesValues = $plotGroup->getPlotValuesByIndex($plotSeriesRef);

            if (($groupType == DataSeries::TYPE_PIECHART) || ($groupType == DataSeries::TYPE_PIECHART_3D) || ($groupType == DataSeries::TYPE_DONUTCHART)) {
                $fillColorValues = $plotSeriesValues->getFillColor();
                if ($fillColorValues !== null && is_array($fillColorValues)) {
                    foreach ($plotSeriesValues->getDataValues() as $dataKey => $dataValue) {
                        $this->writePlotSeriesValuesElement($objWriter, $dataKey, $fillColorValues[$dataKey] ?? '');
                    }
                } else {
                    $this->writePlotSeriesValuesElement($objWriter);
                }
            }

            //    Labels
            $plotSeriesLabel = $plotGroup->getPlotLabelByIndex($plotSeriesRef);
            if ($plotSeriesLabel && ($plotSeriesLabel->getPointCount() > 0)) {
                $objWriter->startElement('c:tx');
                $objWriter->startElement('c:strRef');
                $this->writePlotSeriesLabel($plotSeriesLabel, $objWriter);
                $objWriter->endElement();
                $objWriter->endElement();
            }

            //    Formatting for the points
            if (
                $groupType == DataSeries::TYPE_LINECHART
                || $groupType == DataSeries::TYPE_STOCKCHART
                || ($groupType === DataSeries::TYPE_SCATTERCHART && $plotSeriesValues !== false && !$plotSeriesValues->getScatterLines())
                || ($plotSeriesValues !== false && ($plotSeriesValues->getSchemeClr() || $plotSeriesValues->getPrstClr()))
            ) {
                $plotLineWidth = 12700;
                if ($plotSeriesValues) {
                    $plotLineWidth = $plotSeriesValues->getLineWidth();
                }

                $objWriter->startElement('c:spPr');
                $schemeClr = $typeClr = '';
                if ($plotLabel) {
                    $schemeClr = $plotLabel->getSchemeClr();
                    if ($schemeClr) {
                        $typeClr = 'schemeClr';
                    } else {
                        $schemeClr = $plotLabel->getPrstClr();
                        if ($schemeClr) {
                            $typeClr = 'prstClr';
                        }
                    }
                }
                if ($schemeClr) {
                    $objWriter->startElement('a:solidFill');
                    $objWriter->startElement("a:$typeClr");
                    $objWriter->writeAttribute('val', $schemeClr);
                    $objWriter->endElement();
                    $objWriter->endElement();
                }
                $objWriter->startElement('a:ln');
                $objWriter->writeAttribute('w', $plotLineWidth);
                if ($groupType == DataSeries::TYPE_STOCKCHART || $groupType === DataSeries::TYPE_SCATTERCHART) {
                    $objWriter->startElement('a:noFill');
                    $objWriter->endElement();
                } elseif ($plotLabel) {
                    $fillColor = $plotLabel->getFillColor();
                    if (is_string($fillColor)) {
                        $objWriter->startElement('a:solidFill');
                        $objWriter->startElement('a:srgbClr');
                        $objWriter->writeAttribute('val', $fillColor);
                        $objWriter->endElement();
                        $objWriter->endElement();
                    }
                }
                $objWriter->endElement();
                $objWriter->endElement();
            }

            if ($plotSeriesValues) {
                $plotSeriesMarker = $plotSeriesValues->getPointMarker();
                if ($plotSeriesMarker) {
                    $objWriter->startElement('c:marker');
                    $objWriter->startElement('c:symbol');
                    $objWriter->writeAttribute('val', $plotSeriesMarker);
                    $objWriter->endElement();

                    if ($plotSeriesMarker !== 'none') {
                        $objWriter->startElement('c:size');
                        $objWriter->writeAttribute('val', (string) $plotSeriesValues->getPointSize());
                        $objWriter->endElement();
                        $fillColor = $plotSeriesValues->getFillColor();
                        if (is_string($fillColor) && $fillColor !== '') {
                            $objWriter->startElement('c:spPr');
                            $objWriter->startElement('a:solidFill');
                            $objWriter->startElement('a:srgbClr');
                            $objWriter->writeAttribute('val', $fillColor);
                            $objWriter->endElement(); // srgbClr
                            $objWriter->endElement(); // solidFill
                            $objWriter->endElement(); // spPr
                        }
                    }

                    $objWriter->endElement();
                }
            }

            if (($groupType === DataSeries::TYPE_BARCHART) || ($groupType === DataSeries::TYPE_BARCHART_3D) || ($groupType === DataSeries::TYPE_BUBBLECHART)) {
                $objWriter->startElement('c:invertIfNegative');
                $objWriter->writeAttribute('val', 0);
                $objWriter->endElement();
            }

            //    Category Labels
            $plotSeriesCategory = $plotGroup->getPlotCategoryByIndex($plotSeriesRef);
            if ($plotSeriesCategory && ($plotSeriesCategory->getPointCount() > 0)) {
                $catIsMultiLevelSeries = $catIsMultiLevelSeries || $plotSeriesCategory->isMultiLevelSeries();

                if (($groupType == DataSeries::TYPE_PIECHART) || ($groupType == DataSeries::TYPE_PIECHART_3D) || ($groupType == DataSeries::TYPE_DONUTCHART)) {
                    if ($plotGroup->getPlotStyle() !== null) {
                        $plotStyle = $plotGroup->getPlotStyle();
                        if ($plotStyle) {
                            $objWriter->startElement('c:explosion');
                            $objWriter->writeAttribute('val', 25);
                            $objWriter->endElement();
                        }
                    }
                }

                if (($groupType === DataSeries::TYPE_BUBBLECHART) || ($groupType === DataSeries::TYPE_SCATTERCHART)) {
                    $objWriter->startElement('c:xVal');
                } else {
                    $objWriter->startElement('c:cat');
                }

                // xVals (Categories) are not always 'str'
                // Test X-axis Label's Datatype to decide 'str' vs 'num'
                $CategoryDatatype = $plotSeriesCategory->getDataType();
                if ($CategoryDatatype == DataSeriesValues::DATASERIES_TYPE_NUMBER) {
                    $this->writePlotSeriesValues($plotSeriesCategory, $objWriter, $groupType, 'num');
                } else {
                    $this->writePlotSeriesValues($plotSeriesCategory, $objWriter, $groupType, 'str');
                }
                $objWriter->endElement();
            }

            //    Values
            if ($plotSeriesValues) {
                $valIsMultiLevelSeries = $valIsMultiLevelSeries || $plotSeriesValues->isMultiLevelSeries();

                if (($groupType === DataSeries::TYPE_BUBBLECHART) || ($groupType === DataSeries::TYPE_SCATTERCHART)) {
                    $objWriter->startElement('c:yVal');
                } else {
                    $objWriter->startElement('c:val');
                }

                $this->writePlotSeriesValues($plotSeriesValues, $objWriter, $groupType, 'num');
                $objWriter->endElement();
                if ($groupType === DataSeries::TYPE_SCATTERCHART && $plotGroup->getPlotStyle() === 'smoothMarker') {
                    $objWriter->startElement('c:smooth');
                    $objWriter->writeAttribute('val', '1');
                    $objWriter->endElement();
                }
            }

            if ($groupType === DataSeries::TYPE_BUBBLECHART) {
                if (!empty($plotGroup->getPlotBubbleSizes()[$plotSeriesIdx])) {
                    $objWriter->startElement('c:bubbleSize');
                    $this->writePlotSeriesValues(
                        $plotGroup->getPlotBubbleSizes()[$plotSeriesIdx],
                        $objWriter,
                        $groupType,
                        'num'
                    );
                    $objWriter->endElement();
                    if ($plotSeriesValues !== false) {
                        $objWriter->startElement('c:bubble3D');
                        $objWriter->writeAttribute('val', $plotSeriesValues->getBubble3D() ? '1' : '0');
                        $objWriter->endElement();
                    }
                } else {
                    $this->writeBubbles($plotSeriesValues, $objWriter);
                }
            }

            $objWriter->endElement();
        }

        $this->seriesIndex += $plotSeriesIdx + 1;
    }

    /**
     * Write Plot Series Label.
     */
    private function writePlotSeriesLabel(?DataSeriesValues $plotSeriesLabel, XMLWriter $objWriter): void
    {
        if ($plotSeriesLabel === null) {
            return;
        }

        $objWriter->startElement('c:f');
        $objWriter->writeRawData($plotSeriesLabel->getDataSource());
        $objWriter->endElement();

        $objWriter->startElement('c:strCache');
        $objWriter->startElement('c:ptCount');
        $objWriter->writeAttribute('val', $plotSeriesLabel->getPointCount());
        $objWriter->endElement();

        foreach ($plotSeriesLabel->getDataValues() as $plotLabelKey => $plotLabelValue) {
            $objWriter->startElement('c:pt');
            $objWriter->writeAttribute('idx', $plotLabelKey);

            $objWriter->startElement('c:v');
            $objWriter->writeRawData($plotLabelValue);
            $objWriter->endElement();
            $objWriter->endElement();
        }
        $objWriter->endElement();
    }

    /**
     * Write Plot Series Values.
     *
     * @param string $groupType Type of plot for dataseries
     * @param string $dataType Datatype of series values
     */
    private function writePlotSeriesValues(?DataSeriesValues $plotSeriesValues, XMLWriter $objWriter, $groupType, $dataType = 'str'): void
    {
        if ($plotSeriesValues === null) {
            return;
        }

        if ($plotSeriesValues->isMultiLevelSeries()) {
            $levelCount = $plotSeriesValues->multiLevelCount();

            $objWriter->startElement('c:multiLvlStrRef');

            $objWriter->startElement('c:f');
            $objWriter->writeRawData($plotSeriesValues->getDataSource());
            $objWriter->endElement();

            $objWriter->startElement('c:multiLvlStrCache');

            $objWriter->startElement('c:ptCount');
            $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
            $objWriter->endElement();

            for ($level = 0; $level < $levelCount; ++$level) {
                $objWriter->startElement('c:lvl');

                foreach ($plotSeriesValues->getDataValues() as $plotSeriesKey => $plotSeriesValue) {
                    if (isset($plotSeriesValue[$level])) {
                        $objWriter->startElement('c:pt');
                        $objWriter->writeAttribute('idx', $plotSeriesKey);

                        $objWriter->startElement('c:v');
                        $objWriter->writeRawData($plotSeriesValue[$level]);
                        $objWriter->endElement();
                        $objWriter->endElement();
                    }
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();

            $objWriter->endElement();
        } else {
            $objWriter->startElement('c:' . $dataType . 'Ref');

            $objWriter->startElement('c:f');
            $objWriter->writeRawData($plotSeriesValues->getDataSource());
            $objWriter->endElement();

            $count = $plotSeriesValues->getPointCount();
            $source = $plotSeriesValues->getDataSource();
            $values = $plotSeriesValues->getDataValues();
            if ($count > 1 || ($count === 1 && "=$source" !== (string) $values[0])) {
                $objWriter->startElement('c:' . $dataType . 'Cache');

                if (($groupType != DataSeries::TYPE_PIECHART) && ($groupType != DataSeries::TYPE_PIECHART_3D) && ($groupType != DataSeries::TYPE_DONUTCHART)) {
                    if (($plotSeriesValues->getFormatCode() !== null) && ($plotSeriesValues->getFormatCode() !== '')) {
                        $objWriter->startElement('c:formatCode');
                        $objWriter->writeRawData($plotSeriesValues->getFormatCode());
                        $objWriter->endElement();
                    }
                }

                $objWriter->startElement('c:ptCount');
                $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
                $objWriter->endElement();

                $dataValues = $plotSeriesValues->getDataValues();
                if (!empty($dataValues)) {
                    if (is_array($dataValues)) {
                        foreach ($dataValues as $plotSeriesKey => $plotSeriesValue) {
                            $objWriter->startElement('c:pt');
                            $objWriter->writeAttribute('idx', $plotSeriesKey);

                            $objWriter->startElement('c:v');
                            $objWriter->writeRawData($plotSeriesValue);
                            $objWriter->endElement();
                            $objWriter->endElement();
                        }
                    }
                }

                $objWriter->endElement(); // *Cache
            }

            $objWriter->endElement(); // *Ref
        }
    }

    /**
     * Write Bubble Chart Details.
     */
    private function writeBubbles(?DataSeriesValues $plotSeriesValues, XMLWriter $objWriter): void
    {
        if ($plotSeriesValues === null) {
            return;
        }

        $objWriter->startElement('c:bubbleSize');
        $objWriter->startElement('c:numLit');

        $objWriter->startElement('c:formatCode');
        $objWriter->writeRawData('General');
        $objWriter->endElement();

        $objWriter->startElement('c:ptCount');
        $objWriter->writeAttribute('val', $plotSeriesValues->getPointCount());
        $objWriter->endElement();

        $dataValues = $plotSeriesValues->getDataValues();
        if (!empty($dataValues)) {
            if (is_array($dataValues)) {
                foreach ($dataValues as $plotSeriesKey => $plotSeriesValue) {
                    $objWriter->startElement('c:pt');
                    $objWriter->writeAttribute('idx', $plotSeriesKey);
                    $objWriter->startElement('c:v');
                    $objWriter->writeRawData(1);
                    $objWriter->endElement();
                    $objWriter->endElement();
                }
            }
        }

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('c:bubble3D');
        $objWriter->writeAttribute('val', $plotSeriesValues->getBubble3D() ? '1' : '0');
        $objWriter->endElement();
    }

    /**
     * Write Layout.
     */
    private function writeLayout(XMLWriter $objWriter, ?Layout $layout = null): void
    {
        $objWriter->startElement('c:layout');

        if ($layout !== null) {
            $objWriter->startElement('c:manualLayout');

            $layoutTarget = $layout->getLayoutTarget();
            if ($layoutTarget !== null) {
                $objWriter->startElement('c:layoutTarget');
                $objWriter->writeAttribute('val', $layoutTarget);
                $objWriter->endElement();
            }

            $xMode = $layout->getXMode();
            if ($xMode !== null) {
                $objWriter->startElement('c:xMode');
                $objWriter->writeAttribute('val', $xMode);
                $objWriter->endElement();
            }

            $yMode = $layout->getYMode();
            if ($yMode !== null) {
                $objWriter->startElement('c:yMode');
                $objWriter->writeAttribute('val', $yMode);
                $objWriter->endElement();
            }

            $x = $layout->getXPosition();
            if ($x !== null) {
                $objWriter->startElement('c:x');
                $objWriter->writeAttribute('val', $x);
                $objWriter->endElement();
            }

            $y = $layout->getYPosition();
            if ($y !== null) {
                $objWriter->startElement('c:y');
                $objWriter->writeAttribute('val', $y);
                $objWriter->endElement();
            }

            $w = $layout->getWidth();
            if ($w !== null) {
                $objWriter->startElement('c:w');
                $objWriter->writeAttribute('val', $w);
                $objWriter->endElement();
            }

            $h = $layout->getHeight();
            if ($h !== null) {
                $objWriter->startElement('c:h');
                $objWriter->writeAttribute('val', $h);
                $objWriter->endElement();
            }

            $objWriter->endElement();
        }

        $objWriter->endElement();
    }

    /**
     * Write Alternate Content block.
     */
    private function writeAlternateContent(XMLWriter $objWriter): void
    {
        $objWriter->startElement('mc:AlternateContent');
        $objWriter->writeAttribute('xmlns:mc', 'http://schemas.openxmlformats.org/markup-compatibility/2006');

        $objWriter->startElement('mc:Choice');
        $objWriter->writeAttribute('xmlns:c14', 'http://schemas.microsoft.com/office/drawing/2007/8/2/chart');
        $objWriter->writeAttribute('Requires', 'c14');

        $objWriter->startElement('c14:style');
        $objWriter->writeAttribute('val', '102');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('mc:Fallback');
        $objWriter->startElement('c:style');
        $objWriter->writeAttribute('val', '2');
        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write Printer Settings.
     */
    private function writePrintSettings(XMLWriter $objWriter): void
    {
        $objWriter->startElement('c:printSettings');

        $objWriter->startElement('c:headerFooter');
        $objWriter->endElement();

        $objWriter->startElement('c:pageMargins');
        $objWriter->writeAttribute('footer', 0.3);
        $objWriter->writeAttribute('header', 0.3);
        $objWriter->writeAttribute('r', 0.7);
        $objWriter->writeAttribute('l', 0.7);
        $objWriter->writeAttribute('t', 0.75);
        $objWriter->writeAttribute('b', 0.75);
        $objWriter->endElement();

        $objWriter->startElement('c:pageSetup');
        $objWriter->writeAttribute('orientation', 'portrait');
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write shadow properties.
     *
     * @param Axis|GridLines $xAxis
     */
    private function writeShadow(XMLWriter $objWriter, $xAxis): void
    {
        if (empty($xAxis->getShadowProperty('effect'))) {
            return;
        }
        /** @var string */
        $effect = $xAxis->getShadowProperty('effect');
        $objWriter->startElement("a:$effect");

        if (is_numeric($xAxis->getShadowProperty('blur'))) {
            $objWriter->writeAttribute('blurRad', Properties::pointsToXml((float) $xAxis->getShadowProperty('blur')));
        }
        if (is_numeric($xAxis->getShadowProperty('distance'))) {
            $objWriter->writeAttribute('dist', Properties::pointsToXml((float) $xAxis->getShadowProperty('distance')));
        }
        if (is_numeric($xAxis->getShadowProperty('direction'))) {
            $objWriter->writeAttribute('dir', Properties::angleToXml((float) $xAxis->getShadowProperty('direction')));
        }
        if ($xAxis->getShadowProperty('algn') !== null) {
            $objWriter->writeAttribute('algn', $xAxis->getShadowProperty('algn'));
        }
        foreach (['sx', 'sy'] as $sizeType) {
            $sizeValue = $xAxis->getShadowProperty(['size', $sizeType]);
            if (is_numeric($sizeValue)) {
                $objWriter->writeAttribute($sizeType, Properties::tenthOfPercentToXml((float) $sizeValue));
            }
        }
        foreach (['kx', 'ky'] as $sizeType) {
            $sizeValue = $xAxis->getShadowProperty(['size', $sizeType]);
            if (is_numeric($sizeValue)) {
                $objWriter->writeAttribute($sizeType, Properties::angleToXml((float) $sizeValue));
            }
        }
        if ($xAxis->getShadowProperty('rotWithShape') !== null) {
            $objWriter->writeAttribute('rotWithShape', $xAxis->getShadowProperty('rotWithShape'));
        }

        $objWriter->startElement("a:{$xAxis->getShadowProperty(['color', 'type'])}");
        $objWriter->writeAttribute('val', $xAxis->getShadowProperty(['color', 'value']));
        $alpha = $xAxis->getShadowProperty(['color', 'alpha']);
        if (is_numeric($alpha)) {
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', Properties::alphaToXml((int) $alpha));
            $objWriter->endElement();
        }
        $objWriter->endElement();

        $objWriter->endElement();
    }

    /**
     * Write glow properties.
     *
     * @param Axis|GridLines $yAxis
     */
    private function writeGlow(XMLWriter $objWriter, $yAxis): void
    {
        $size = $yAxis->getGlowProperty('size');
        if (empty($size)) {
            return;
        }
        $objWriter->startElement('a:glow');
        $objWriter->writeAttribute('rad', Properties::pointsToXml((float) $size));
        $objWriter->startElement("a:{$yAxis->getGlowProperty(['color', 'type'])}");
        $objWriter->writeAttribute('val', (string) $yAxis->getGlowProperty(['color', 'value']));
        $alpha = $yAxis->getGlowProperty(['color', 'alpha']);
        if (is_numeric($alpha)) {
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', Properties::alphaToXml((int) $alpha));
            $objWriter->endElement(); // alpha
        }
        $objWriter->endElement(); // color
        $objWriter->endElement(); // glow
    }

    /**
     * Write soft edge properties.
     *
     * @param Axis|GridLines $yAxis
     */
    private function writeSoftEdge(XMLWriter $objWriter, $yAxis): void
    {
        $softEdgeSize = $yAxis->getSoftEdgesSize();
        if (empty($softEdgeSize)) {
            return;
        }
        $objWriter->startElement('a:softEdge');
        $objWriter->writeAttribute('rad', Properties::pointsToXml((float) $softEdgeSize));
        $objWriter->endElement(); //end softEdge
    }

    /**
     * Write Line Style for Gridlines.
     *
     * @param Axis|GridLines $gridlines
     */
    private function writeGridlinesLn(XMLWriter $objWriter, $gridlines): void
    {
        $objWriter->startElement('a:ln');
        $widthTemp = $gridlines->getLineStyleProperty('width');
        if (is_numeric($widthTemp)) {
            $objWriter->writeAttribute('w', Properties::pointsToXml((float) $widthTemp));
        }
        $this->writeNotEmpty($objWriter, 'cap', $gridlines->getLineStyleProperty('cap'));
        $this->writeNotEmpty($objWriter, 'cmpd', $gridlines->getLineStyleProperty('compound'));
        if (!empty($gridlines->getLineColorProperty('value'))) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement("a:{$gridlines->getLineColorProperty('type')}");
            $objWriter->writeAttribute('val', (string) $gridlines->getLineColorProperty('value'));
            $alpha = $gridlines->getLineColorProperty('alpha');
            if (is_numeric($alpha)) {
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', Properties::alphaToXml((int) $alpha));
                $objWriter->endElement(); // alpha
            }
            $objWriter->endElement(); //end srgbClr
            $objWriter->endElement(); //end solidFill
        }

        $dash = $gridlines->getLineStyleProperty('dash');
        if (!empty($dash)) {
            $objWriter->startElement('a:prstDash');
            $this->writeNotEmpty($objWriter, 'val', $dash);
            $objWriter->endElement();
        }

        if ($gridlines->getLineStyleProperty('join') === 'miter') {
            $objWriter->startElement('a:miter');
            $objWriter->writeAttribute('lim', '800000');
            $objWriter->endElement();
        } elseif ($gridlines->getLineStyleProperty('join') === 'bevel') {
            $objWriter->startElement('a:bevel');
            $objWriter->endElement();
        }

        if ($gridlines->getLineStyleProperty(['arrow', 'head', 'type'])) {
            $objWriter->startElement('a:headEnd');
            $objWriter->writeAttribute('type', $gridlines->getLineStyleProperty(['arrow', 'head', 'type']));
            $this->writeNotEmpty($objWriter, 'w', $gridlines->getLineStyleArrowParameters('head', 'w'));
            $this->writeNotEmpty($objWriter, 'len', $gridlines->getLineStyleArrowParameters('head', 'len'));
            $objWriter->endElement();
        }

        if ($gridlines->getLineStyleProperty(['arrow', 'end', 'type'])) {
            $objWriter->startElement('a:tailEnd');
            $objWriter->writeAttribute('type', $gridlines->getLineStyleProperty(['arrow', 'end', 'type']));
            $this->writeNotEmpty($objWriter, 'w', $gridlines->getLineStyleArrowParameters('end', 'w'));
            $this->writeNotEmpty($objWriter, 'len', $gridlines->getLineStyleArrowParameters('end', 'len'));
            $objWriter->endElement();
        }
        $objWriter->endElement(); //end ln
    }

    private function writeNotEmpty(XMLWriter $objWriter, string $name, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $objWriter->writeAttribute($name, $value);
        }
    }
}

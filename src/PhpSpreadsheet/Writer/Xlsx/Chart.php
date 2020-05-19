<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
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
    public function writeChart(\PhpOffice\PhpSpreadsheet\Chart\Chart $pChart, $calculateCellValues = true)
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
            $pChart->refresh();
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

        $this->writeTitle($objWriter, $pChart->getTitle());

        $objWriter->startElement('c:autoTitleDeleted');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $this->writePlotArea($objWriter, $pChart->getWorksheet(), $pChart->getPlotArea(), $pChart->getXAxisLabel(), $pChart->getYAxisLabel(), $pChart->getChartAxisX(), $pChart->getChartAxisY(), $pChart->getMajorGridlines(), $pChart->getMinorGridlines());

        $this->writeLegend($objWriter, $pChart->getLegend());

        $objWriter->startElement('c:plotVisOnly');
        $objWriter->writeAttribute('val', (int) $pChart->getPlotVisibleOnly());
        $objWriter->endElement();

        $objWriter->startElement('c:dispBlanksAs');
        $objWriter->writeAttribute('val', $pChart->getDisplayBlanksAs());
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
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Title $title
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

        $caption = $title->getCaption();
        if ((is_array($caption)) && (count($caption) > 0)) {
            $caption = $caption[0];
        }
        $this->getParentWriter()->getWriterPart('stringtable')->writeRichTextForCharts($objWriter, $caption, 'a');

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
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Legend $legend
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
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Title $xAxisLabel
     * @param Title $yAxisLabel
     * @param Axis $xAxis
     * @param Axis $yAxis
     */
    private function writePlotArea(XMLWriter $objWriter, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $pSheet, PlotArea $plotArea, ?Title $xAxisLabel = null, ?Title $yAxisLabel = null, ?Axis $xAxis = null, ?Axis $yAxis = null, ?GridLines $majorGridlines = null, ?GridLines $minorGridlines = null): void
    {
        if ($plotArea === null) {
            return;
        }

        $id1 = $id2 = 0;
        $this->seriesIndex = 0;
        $objWriter->startElement('c:plotArea');

        $layout = $plotArea->getLayout();

        $this->writeLayout($objWriter, $layout);

        $chartTypes = self::getChartType($plotArea);
        $catIsMultiLevelSeries = $valIsMultiLevelSeries = false;
        $plotGroupingType = '';
        foreach ($chartTypes as $chartType) {
            $objWriter->startElement('c:' . $chartType);

            $groupCount = $plotArea->getPlotGroupCount();
            for ($i = 0; $i < $groupCount; ++$i) {
                $plotGroup = $plotArea->getPlotGroupByIndex($i);
                $groupType = $plotGroup->getPlotType();
                if ($groupType == $chartType) {
                    $plotStyle = $plotGroup->getPlotStyle();
                    if ($groupType === DataSeries::TYPE_RADARCHART) {
                        $objWriter->startElement('c:radarStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    } elseif ($groupType === DataSeries::TYPE_SCATTERCHART) {
                        $objWriter->startElement('c:scatterStyle');
                        $objWriter->writeAttribute('val', $plotStyle);
                        $objWriter->endElement();
                    }

                    $this->writePlotGroup($plotGroup, $chartType, $objWriter, $catIsMultiLevelSeries, $valIsMultiLevelSeries, $plotGroupingType);
                }
            }

            $this->writeDataLabels($objWriter, $layout);

            if ($chartType === DataSeries::TYPE_LINECHART) {
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
                $objWriter->startElement('c:bubbleScale');
                $objWriter->writeAttribute('val', 25);
                $objWriter->endElement();

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

            //    Generate 2 unique numbers to use for axId values
            $id1 = '75091328';
            $id2 = '75089408';

            if (($chartType !== DataSeries::TYPE_PIECHART) && ($chartType !== DataSeries::TYPE_PIECHART_3D) && ($chartType !== DataSeries::TYPE_DONUTCHART)) {
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id1);
                $objWriter->endElement();
                $objWriter->startElement('c:axId');
                $objWriter->writeAttribute('val', $id2);
                $objWriter->endElement();
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
                $this->writeValueAxis($objWriter, $xAxisLabel, $chartType, $id1, $id2, $catIsMultiLevelSeries, $xAxis, $majorGridlines, $minorGridlines);
            } else {
                $this->writeCategoryAxis($objWriter, $xAxisLabel, $id1, $id2, $catIsMultiLevelSeries, $yAxis);
            }

            $this->writeValueAxis($objWriter, $yAxisLabel, $chartType, $id1, $id2, $valIsMultiLevelSeries, $xAxis, $majorGridlines, $minorGridlines);
        }

        $objWriter->endElement();
    }

    /**
     * Write Data Labels.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param \PhpOffice\PhpSpreadsheet\Chart\Layout $chartLayout Chart layout
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
     * @param XMLWriter $objWriter XML Writer
     * @param Title $xAxisLabel
     * @param string $id1
     * @param string $id2
     * @param bool $isMultiLevelSeries
     */
    private function writeCategoryAxis($objWriter, $xAxisLabel, $id1, $id2, $isMultiLevelSeries, Axis $yAxis): void
    {
        $objWriter->startElement('c:catAx');

        if ($id1 > 0) {
            $objWriter->startElement('c:axId');
            $objWriter->writeAttribute('val', $id1);
            $objWriter->endElement();
        }

        $objWriter->startElement('c:scaling');
        $objWriter->startElement('c:orientation');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('orientation'));
        $objWriter->endElement();
        $objWriter->endElement();

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
            $objWriter->startElement('a:r');

            $caption = $xAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }
            $objWriter->startElement('a:t');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($caption));
            $objWriter->endElement();

            $objWriter->endElement();
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

        $objWriter->startElement('c:majorTickMark');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('major_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:minorTickMark');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('minor_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:tickLblPos');
        $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('axis_labels'));
        $objWriter->endElement();

        if ($id2 > 0) {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();

            $objWriter->startElement('c:crosses');
            $objWriter->writeAttribute('val', $yAxis->getAxisOptionsProperty('horizontal_crosses'));
            $objWriter->endElement();
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
     * @param XMLWriter $objWriter XML Writer
     * @param Title $yAxisLabel
     * @param string $groupType Chart type
     * @param string $id1
     * @param string $id2
     * @param bool $isMultiLevelSeries
     */
    private function writeValueAxis($objWriter, $yAxisLabel, $groupType, $id1, $id2, $isMultiLevelSeries, Axis $xAxis, GridLines $majorGridlines, GridLines $minorGridlines): void
    {
        $objWriter->startElement('c:valAx');

        if ($id2 > 0) {
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

        $objWriter->startElement('c:orientation');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('orientation'));

        $objWriter->endElement();
        $objWriter->endElement();

        $objWriter->startElement('c:delete');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:axPos');
        $objWriter->writeAttribute('val', 'l');
        $objWriter->endElement();

        $objWriter->startElement('c:majorGridlines');
        $objWriter->startElement('c:spPr');

        if ($majorGridlines->getLineColorProperty('value') !== null) {
            $objWriter->startElement('a:ln');
            $objWriter->writeAttribute('w', $majorGridlines->getLineStyleProperty('width'));
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement("a:{$majorGridlines->getLineColorProperty('type')}");
            $objWriter->writeAttribute('val', $majorGridlines->getLineColorProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getLineColorProperty('alpha'));
            $objWriter->endElement(); //end alpha
            $objWriter->endElement(); //end srgbClr
            $objWriter->endElement(); //end solidFill

            $objWriter->startElement('a:prstDash');
            $objWriter->writeAttribute('val', $majorGridlines->getLineStyleProperty('dash'));
            $objWriter->endElement();

            if ($majorGridlines->getLineStyleProperty('join') == 'miter') {
                $objWriter->startElement('a:miter');
                $objWriter->writeAttribute('lim', '800000');
                $objWriter->endElement();
            } else {
                $objWriter->startElement('a:bevel');
                $objWriter->endElement();
            }

            if ($majorGridlines->getLineStyleProperty(['arrow', 'head', 'type']) !== null) {
                $objWriter->startElement('a:headEnd');
                $objWriter->writeAttribute('type', $majorGridlines->getLineStyleProperty(['arrow', 'head', 'type']));
                $objWriter->writeAttribute('w', $majorGridlines->getLineStyleArrowParameters('head', 'w'));
                $objWriter->writeAttribute('len', $majorGridlines->getLineStyleArrowParameters('head', 'len'));
                $objWriter->endElement();
            }

            if ($majorGridlines->getLineStyleProperty(['arrow', 'end', 'type']) !== null) {
                $objWriter->startElement('a:tailEnd');
                $objWriter->writeAttribute('type', $majorGridlines->getLineStyleProperty(['arrow', 'end', 'type']));
                $objWriter->writeAttribute('w', $majorGridlines->getLineStyleArrowParameters('end', 'w'));
                $objWriter->writeAttribute('len', $majorGridlines->getLineStyleArrowParameters('end', 'len'));
                $objWriter->endElement();
            }
            $objWriter->endElement(); //end ln
        }
        $objWriter->startElement('a:effectLst');

        if ($majorGridlines->getGlowSize() !== null) {
            $objWriter->startElement('a:glow');
            $objWriter->writeAttribute('rad', $majorGridlines->getGlowSize());
            $objWriter->startElement("a:{$majorGridlines->getGlowColor('type')}");
            $objWriter->writeAttribute('val', $majorGridlines->getGlowColor('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getGlowColor('alpha'));
            $objWriter->endElement(); //end alpha
            $objWriter->endElement(); //end schemeClr
            $objWriter->endElement(); //end glow
        }

        if ($majorGridlines->getShadowProperty('presets') !== null) {
            $objWriter->startElement("a:{$majorGridlines->getShadowProperty('effect')}");
            if ($majorGridlines->getShadowProperty('blur') !== null) {
                $objWriter->writeAttribute('blurRad', $majorGridlines->getShadowProperty('blur'));
            }
            if ($majorGridlines->getShadowProperty('distance') !== null) {
                $objWriter->writeAttribute('dist', $majorGridlines->getShadowProperty('distance'));
            }
            if ($majorGridlines->getShadowProperty('direction') !== null) {
                $objWriter->writeAttribute('dir', $majorGridlines->getShadowProperty('direction'));
            }
            if ($majorGridlines->getShadowProperty('algn') !== null) {
                $objWriter->writeAttribute('algn', $majorGridlines->getShadowProperty('algn'));
            }
            if ($majorGridlines->getShadowProperty(['size', 'sx']) !== null) {
                $objWriter->writeAttribute('sx', $majorGridlines->getShadowProperty(['size', 'sx']));
            }
            if ($majorGridlines->getShadowProperty(['size', 'sy']) !== null) {
                $objWriter->writeAttribute('sy', $majorGridlines->getShadowProperty(['size', 'sy']));
            }
            if ($majorGridlines->getShadowProperty(['size', 'kx']) !== null) {
                $objWriter->writeAttribute('kx', $majorGridlines->getShadowProperty(['size', 'kx']));
            }
            if ($majorGridlines->getShadowProperty('rotWithShape') !== null) {
                $objWriter->writeAttribute('rotWithShape', $majorGridlines->getShadowProperty('rotWithShape'));
            }
            $objWriter->startElement("a:{$majorGridlines->getShadowProperty(['color', 'type'])}");
            $objWriter->writeAttribute('val', $majorGridlines->getShadowProperty(['color', 'value']));

            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $majorGridlines->getShadowProperty(['color', 'alpha']));
            $objWriter->endElement(); //end alpha

            $objWriter->endElement(); //end color:type
            $objWriter->endElement(); //end shadow
        }

        if ($majorGridlines->getSoftEdgesSize() !== null) {
            $objWriter->startElement('a:softEdge');
            $objWriter->writeAttribute('rad', $majorGridlines->getSoftEdgesSize());
            $objWriter->endElement(); //end softEdge
        }

        $objWriter->endElement(); //end effectLst
        $objWriter->endElement(); //end spPr
        $objWriter->endElement(); //end majorGridLines

        if ($minorGridlines->getObjectState()) {
            $objWriter->startElement('c:minorGridlines');
            $objWriter->startElement('c:spPr');

            if ($minorGridlines->getLineColorProperty('value') !== null) {
                $objWriter->startElement('a:ln');
                $objWriter->writeAttribute('w', $minorGridlines->getLineStyleProperty('width'));
                $objWriter->startElement('a:solidFill');
                $objWriter->startElement("a:{$minorGridlines->getLineColorProperty('type')}");
                $objWriter->writeAttribute('val', $minorGridlines->getLineColorProperty('value'));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getLineColorProperty('alpha'));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end srgbClr
                $objWriter->endElement(); //end solidFill

                $objWriter->startElement('a:prstDash');
                $objWriter->writeAttribute('val', $minorGridlines->getLineStyleProperty('dash'));
                $objWriter->endElement();

                if ($minorGridlines->getLineStyleProperty('join') == 'miter') {
                    $objWriter->startElement('a:miter');
                    $objWriter->writeAttribute('lim', '800000');
                    $objWriter->endElement();
                } else {
                    $objWriter->startElement('a:bevel');
                    $objWriter->endElement();
                }

                if ($minorGridlines->getLineStyleProperty(['arrow', 'head', 'type']) !== null) {
                    $objWriter->startElement('a:headEnd');
                    $objWriter->writeAttribute('type', $minorGridlines->getLineStyleProperty(['arrow', 'head', 'type']));
                    $objWriter->writeAttribute('w', $minorGridlines->getLineStyleArrowParameters('head', 'w'));
                    $objWriter->writeAttribute('len', $minorGridlines->getLineStyleArrowParameters('head', 'len'));
                    $objWriter->endElement();
                }

                if ($minorGridlines->getLineStyleProperty(['arrow', 'end', 'type']) !== null) {
                    $objWriter->startElement('a:tailEnd');
                    $objWriter->writeAttribute('type', $minorGridlines->getLineStyleProperty(['arrow', 'end', 'type']));
                    $objWriter->writeAttribute('w', $minorGridlines->getLineStyleArrowParameters('end', 'w'));
                    $objWriter->writeAttribute('len', $minorGridlines->getLineStyleArrowParameters('end', 'len'));
                    $objWriter->endElement();
                }
                $objWriter->endElement(); //end ln
            }

            $objWriter->startElement('a:effectLst');

            if ($minorGridlines->getGlowSize() !== null) {
                $objWriter->startElement('a:glow');
                $objWriter->writeAttribute('rad', $minorGridlines->getGlowSize());
                $objWriter->startElement("a:{$minorGridlines->getGlowColor('type')}");
                $objWriter->writeAttribute('val', $minorGridlines->getGlowColor('value'));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getGlowColor('alpha'));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end schemeClr
                $objWriter->endElement(); //end glow
            }

            if ($minorGridlines->getShadowProperty('presets') !== null) {
                $objWriter->startElement("a:{$minorGridlines->getShadowProperty('effect')}");
                if ($minorGridlines->getShadowProperty('blur') !== null) {
                    $objWriter->writeAttribute('blurRad', $minorGridlines->getShadowProperty('blur'));
                }
                if ($minorGridlines->getShadowProperty('distance') !== null) {
                    $objWriter->writeAttribute('dist', $minorGridlines->getShadowProperty('distance'));
                }
                if ($minorGridlines->getShadowProperty('direction') !== null) {
                    $objWriter->writeAttribute('dir', $minorGridlines->getShadowProperty('direction'));
                }
                if ($minorGridlines->getShadowProperty('algn') !== null) {
                    $objWriter->writeAttribute('algn', $minorGridlines->getShadowProperty('algn'));
                }
                if ($minorGridlines->getShadowProperty(['size', 'sx']) !== null) {
                    $objWriter->writeAttribute('sx', $minorGridlines->getShadowProperty(['size', 'sx']));
                }
                if ($minorGridlines->getShadowProperty(['size', 'sy']) !== null) {
                    $objWriter->writeAttribute('sy', $minorGridlines->getShadowProperty(['size', 'sy']));
                }
                if ($minorGridlines->getShadowProperty(['size', 'kx']) !== null) {
                    $objWriter->writeAttribute('kx', $minorGridlines->getShadowProperty(['size', 'kx']));
                }
                if ($minorGridlines->getShadowProperty('rotWithShape') !== null) {
                    $objWriter->writeAttribute('rotWithShape', $minorGridlines->getShadowProperty('rotWithShape'));
                }
                $objWriter->startElement("a:{$minorGridlines->getShadowProperty(['color', 'type'])}");
                $objWriter->writeAttribute('val', $minorGridlines->getShadowProperty(['color', 'value']));
                $objWriter->startElement('a:alpha');
                $objWriter->writeAttribute('val', $minorGridlines->getShadowProperty(['color', 'alpha']));
                $objWriter->endElement(); //end alpha
                $objWriter->endElement(); //end color:type
                $objWriter->endElement(); //end shadow
            }

            if ($minorGridlines->getSoftEdgesSize() !== null) {
                $objWriter->startElement('a:softEdge');
                $objWriter->writeAttribute('rad', $minorGridlines->getSoftEdgesSize());
                $objWriter->endElement(); //end softEdge
            }

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
            $objWriter->startElement('a:r');

            $caption = $yAxisLabel->getCaption();
            if (is_array($caption)) {
                $caption = $caption[0];
            }

            $objWriter->startElement('a:t');
            $objWriter->writeRawData(StringHelper::controlCharacterPHP2OOXML($caption));
            $objWriter->endElement();

            $objWriter->endElement();
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

        $objWriter->startElement('c:majorTickMark');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('major_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:minorTickMark');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('minor_tick_mark'));
        $objWriter->endElement();

        $objWriter->startElement('c:tickLblPos');
        $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('axis_labels'));
        $objWriter->endElement();

        $objWriter->startElement('c:spPr');

        if ($xAxis->getFillProperty('value') !== null) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement('a:' . $xAxis->getFillProperty('type'));
            $objWriter->writeAttribute('val', $xAxis->getFillProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getFillProperty('alpha'));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        $objWriter->startElement('a:ln');

        $objWriter->writeAttribute('w', $xAxis->getLineStyleProperty('width'));
        $objWriter->writeAttribute('cap', $xAxis->getLineStyleProperty('cap'));
        $objWriter->writeAttribute('cmpd', $xAxis->getLineStyleProperty('compound'));

        if ($xAxis->getLineProperty('value') !== null) {
            $objWriter->startElement('a:solidFill');
            $objWriter->startElement('a:' . $xAxis->getLineProperty('type'));
            $objWriter->writeAttribute('val', $xAxis->getLineProperty('value'));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getLineProperty('alpha'));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        $objWriter->startElement('a:prstDash');
        $objWriter->writeAttribute('val', $xAxis->getLineStyleProperty('dash'));
        $objWriter->endElement();

        if ($xAxis->getLineStyleProperty('join') == 'miter') {
            $objWriter->startElement('a:miter');
            $objWriter->writeAttribute('lim', '800000');
            $objWriter->endElement();
        } else {
            $objWriter->startElement('a:bevel');
            $objWriter->endElement();
        }

        if ($xAxis->getLineStyleProperty(['arrow', 'head', 'type']) !== null) {
            $objWriter->startElement('a:headEnd');
            $objWriter->writeAttribute('type', $xAxis->getLineStyleProperty(['arrow', 'head', 'type']));
            $objWriter->writeAttribute('w', $xAxis->getLineStyleArrowWidth('head'));
            $objWriter->writeAttribute('len', $xAxis->getLineStyleArrowLength('head'));
            $objWriter->endElement();
        }

        if ($xAxis->getLineStyleProperty(['arrow', 'end', 'type']) !== null) {
            $objWriter->startElement('a:tailEnd');
            $objWriter->writeAttribute('type', $xAxis->getLineStyleProperty(['arrow', 'end', 'type']));
            $objWriter->writeAttribute('w', $xAxis->getLineStyleArrowWidth('end'));
            $objWriter->writeAttribute('len', $xAxis->getLineStyleArrowLength('end'));
            $objWriter->endElement();
        }

        $objWriter->endElement();

        $objWriter->startElement('a:effectLst');

        if ($xAxis->getGlowProperty('size') !== null) {
            $objWriter->startElement('a:glow');
            $objWriter->writeAttribute('rad', $xAxis->getGlowProperty('size'));
            $objWriter->startElement("a:{$xAxis->getGlowProperty(['color', 'type'])}");
            $objWriter->writeAttribute('val', $xAxis->getGlowProperty(['color', 'value']));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getGlowProperty(['color', 'alpha']));
            $objWriter->endElement();
            $objWriter->endElement();
            $objWriter->endElement();
        }

        if ($xAxis->getShadowProperty('presets') !== null) {
            $objWriter->startElement("a:{$xAxis->getShadowProperty('effect')}");

            if ($xAxis->getShadowProperty('blur') !== null) {
                $objWriter->writeAttribute('blurRad', $xAxis->getShadowProperty('blur'));
            }
            if ($xAxis->getShadowProperty('distance') !== null) {
                $objWriter->writeAttribute('dist', $xAxis->getShadowProperty('distance'));
            }
            if ($xAxis->getShadowProperty('direction') !== null) {
                $objWriter->writeAttribute('dir', $xAxis->getShadowProperty('direction'));
            }
            if ($xAxis->getShadowProperty('algn') !== null) {
                $objWriter->writeAttribute('algn', $xAxis->getShadowProperty('algn'));
            }
            if ($xAxis->getShadowProperty(['size', 'sx']) !== null) {
                $objWriter->writeAttribute('sx', $xAxis->getShadowProperty(['size', 'sx']));
            }
            if ($xAxis->getShadowProperty(['size', 'sy']) !== null) {
                $objWriter->writeAttribute('sy', $xAxis->getShadowProperty(['size', 'sy']));
            }
            if ($xAxis->getShadowProperty(['size', 'kx']) !== null) {
                $objWriter->writeAttribute('kx', $xAxis->getShadowProperty(['size', 'kx']));
            }
            if ($xAxis->getShadowProperty('rotWithShape') !== null) {
                $objWriter->writeAttribute('rotWithShape', $xAxis->getShadowProperty('rotWithShape'));
            }

            $objWriter->startElement("a:{$xAxis->getShadowProperty(['color', 'type'])}");
            $objWriter->writeAttribute('val', $xAxis->getShadowProperty(['color', 'value']));
            $objWriter->startElement('a:alpha');
            $objWriter->writeAttribute('val', $xAxis->getShadowProperty(['color', 'alpha']));
            $objWriter->endElement();
            $objWriter->endElement();

            $objWriter->endElement();
        }

        if ($xAxis->getSoftEdgesSize() !== null) {
            $objWriter->startElement('a:softEdge');
            $objWriter->writeAttribute('rad', $xAxis->getSoftEdgesSize());
            $objWriter->endElement();
        }

        $objWriter->endElement(); //effectList
        $objWriter->endElement(); //end spPr

        if ($id1 > 0) {
            $objWriter->startElement('c:crossAx');
            $objWriter->writeAttribute('val', $id2);
            $objWriter->endElement();

            if ($xAxis->getAxisOptionsProperty('horizontal_crosses_value') !== null) {
                $objWriter->startElement('c:crossesAt');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('horizontal_crosses_value'));
                $objWriter->endElement();
            } else {
                $objWriter->startElement('c:crosses');
                $objWriter->writeAttribute('val', $xAxis->getAxisOptionsProperty('horizontal_crosses'));
                $objWriter->endElement();
            }

            $objWriter->startElement('c:crossBetween');
            $objWriter->writeAttribute('val', 'midCat');
            $objWriter->endElement();

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
     * Get the data series type(s) for a chart plot series.
     *
     * @param PlotArea $plotArea
     *
     * @return array|string
     */
    private static function getChartType($plotArea)
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
     * @param XMLWriter $objWriter XML Writer
     * @param int       $val       value for idx (default: 3)
     * @param string    $fillColor hex color (default: FF9900)
     *
     * @return XMLWriter XML Writer
     */
    private function writePlotSeriesValuesElement($objWriter, $val = 3, $fillColor = 'FF9900')
    {
        $objWriter->startElement('c:dPt');
        $objWriter->startElement('c:idx');
        $objWriter->writeAttribute('val', $val);
        $objWriter->endElement();

        $objWriter->startElement('c:bubble3D');
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();

        $objWriter->startElement('c:spPr');
        $objWriter->startElement('a:solidFill');
        $objWriter->startElement('a:srgbClr');
        $objWriter->writeAttribute('val', $fillColor);
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter;
    }

    /**
     * Write Plot Group (series of related plots).
     *
     * @param DataSeries $plotGroup
     * @param string $groupType Type of plot for dataseries
     * @param XMLWriter $objWriter XML Writer
     * @param bool &$catIsMultiLevelSeries Is category a multi-series category
     * @param bool &$valIsMultiLevelSeries Is value set a multi-series set
     * @param string &$plotGroupingType Type of grouping for multi-series values
     */
    private function writePlotGroup($plotGroup, $groupType, $objWriter, &$catIsMultiLevelSeries, &$valIsMultiLevelSeries, &$plotGroupingType): void
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

        foreach ($plotSeriesOrder as $plotSeriesIdx => $plotSeriesRef) {
            $objWriter->startElement('c:ser');

            $plotLabel = $plotGroup->getPlotLabelByIndex($plotSeriesIdx);
            if ($plotLabel) {
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
                        $this->writePlotSeriesValuesElement($objWriter, $dataKey, ($fillColorValues[$dataKey] ?? 'FF9900'));
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
            if (($groupType == DataSeries::TYPE_LINECHART) || ($groupType == DataSeries::TYPE_STOCKCHART)) {
                $plotLineWidth = 12700;
                if ($plotSeriesValues) {
                    $plotLineWidth = $plotSeriesValues->getLineWidth();
                }

                $objWriter->startElement('c:spPr');
                $objWriter->startElement('a:ln');
                $objWriter->writeAttribute('w', $plotLineWidth);
                if ($groupType == DataSeries::TYPE_STOCKCHART) {
                    $objWriter->startElement('a:noFill');
                    $objWriter->endElement();
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
                        $objWriter->writeAttribute('val', 3);
                        $objWriter->endElement();
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

                $this->writePlotSeriesValues($plotSeriesCategory, $objWriter, $groupType, 'str');
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
            }

            if ($groupType === DataSeries::TYPE_BUBBLECHART) {
                $this->writeBubbles($plotSeriesValues, $objWriter);
            }

            $objWriter->endElement();
        }

        $this->seriesIndex += $plotSeriesIdx + 1;
    }

    /**
     * Write Plot Series Label.
     *
     * @param DataSeriesValues $plotSeriesLabel
     * @param XMLWriter $objWriter XML Writer
     */
    private function writePlotSeriesLabel($plotSeriesLabel, $objWriter): void
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
     * @param DataSeriesValues $plotSeriesValues
     * @param XMLWriter $objWriter XML Writer
     * @param string $groupType Type of plot for dataseries
     * @param string $dataType Datatype of series values
     */
    private function writePlotSeriesValues($plotSeriesValues, XMLWriter $objWriter, $groupType, $dataType = 'str'): void
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

            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write Bubble Chart Details.
     *
     * @param DataSeriesValues $plotSeriesValues
     * @param XMLWriter $objWriter XML Writer
     */
    private function writeBubbles($plotSeriesValues, $objWriter): void
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
        $objWriter->writeAttribute('val', 0);
        $objWriter->endElement();
    }

    /**
     * Write Layout.
     *
     * @param XMLWriter $objWriter XML Writer
     * @param Layout $layout
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
     *
     * @param XMLWriter $objWriter XML Writer
     */
    private function writeAlternateContent($objWriter): void
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
     *
     * @param XMLWriter $objWriter XML Writer
     */
    private function writePrintSettings($objWriter): void
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
}

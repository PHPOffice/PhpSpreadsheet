<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Properties as ChartProperties;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\TrendLine;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use SimpleXMLElement;

class Chart
{
    /** @var string */
    private $cNamespace;

    /** @var string */
    private $aNamespace;

    public function __construct(string $cNamespace = Namespaces::CHART, string $aNamespace = Namespaces::DRAWINGML)
    {
        $this->cNamespace = $cNamespace;
        $this->aNamespace = $aNamespace;
    }

    /**
     * @param string $name
     * @param string $format
     *
     * @return null|bool|float|int|string
     */
    private static function getAttribute(SimpleXMLElement $component, $name, $format)
    {
        $attributes = $component->attributes();
        if (@isset($attributes[$name])) {
            if ($format == 'string') {
                return (string) $attributes[$name];
            } elseif ($format == 'integer') {
                return (int) $attributes[$name];
            } elseif ($format == 'boolean') {
                $value = (string) $attributes[$name];

                return $value === 'true' || $value === '1';
            }

            return (float) $attributes[$name];
        }

        return null;
    }

    /**
     * @param string $chartName
     *
     * @return \PhpOffice\PhpSpreadsheet\Chart\Chart
     */
    public function readChart(SimpleXMLElement $chartElements, $chartName)
    {
        $chartElementsC = $chartElements->children($this->cNamespace);

        $XaxisLabel = $YaxisLabel = $legend = $title = null;
        $dispBlanksAs = $plotVisOnly = null;
        $plotArea = null;
        $rotX = $rotY = $rAngAx = $perspective = null;
        $xAxis = new Axis();
        $yAxis = new Axis();
        $autoTitleDeleted = null;
        $chartNoFill = false;
        $gradientArray = [];
        $gradientLin = null;
        $roundedCorners = false;
        foreach ($chartElementsC as $chartElementKey => $chartElement) {
            switch ($chartElementKey) {
                case 'spPr':
                    $possibleNoFill = $chartElementsC->spPr->children($this->aNamespace);
                    if (isset($possibleNoFill->noFill)) {
                        $chartNoFill = true;
                    }

                    break;
                case 'roundedCorners':
                    /** @var bool */
                    $roundedCorners = self::getAttribute($chartElementsC->roundedCorners, 'val', 'boolean');

                    break;
                case 'chart':
                    foreach ($chartElement as $chartDetailsKey => $chartDetails) {
                        $chartDetails = Xlsx::testSimpleXml($chartDetails);
                        switch ($chartDetailsKey) {
                            case 'autoTitleDeleted':
                                /** @var bool */
                                $autoTitleDeleted = self::getAttribute($chartElementsC->chart->autoTitleDeleted, 'val', 'boolean');

                                break;
                            case 'view3D':
                                $rotX = self::getAttribute($chartDetails->rotX, 'val', 'integer');
                                $rotY = self::getAttribute($chartDetails->rotY, 'val', 'integer');
                                $rAngAx = self::getAttribute($chartDetails->rAngAx, 'val', 'integer');
                                $perspective = self::getAttribute($chartDetails->perspective, 'val', 'integer');

                                break;
                            case 'plotArea':
                                $plotAreaLayout = $XaxisLabel = $YaxisLabel = null;
                                $plotSeries = $plotAttributes = [];
                                $catAxRead = false;
                                $plotNoFill = false;
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    $chartDetail = Xlsx::testSimpleXml($chartDetail);
                                    switch ($chartDetailKey) {
                                        case 'spPr':
                                            $possibleNoFill = $chartDetails->spPr->children($this->aNamespace);
                                            if (isset($possibleNoFill->noFill)) {
                                                $plotNoFill = true;
                                            }
                                            if (isset($possibleNoFill->gradFill->gsLst)) {
                                                foreach ($possibleNoFill->gradFill->gsLst->gs as $gradient) {
                                                    $gradient = Xlsx::testSimpleXml($gradient);
                                                    /** @var float */
                                                    $pos = self::getAttribute($gradient, 'pos', 'float');
                                                    $gradientArray[] = [
                                                        $pos / ChartProperties::PERCENTAGE_MULTIPLIER,
                                                        new ChartColor($this->readColor($gradient)),
                                                    ];
                                                }
                                            }
                                            if (isset($possibleNoFill->gradFill->lin)) {
                                                $gradientLin = ChartProperties::XmlToAngle((string) self::getAttribute($possibleNoFill->gradFill->lin, 'ang', 'string'));
                                            }

                                            break;
                                        case 'layout':
                                            $plotAreaLayout = $this->chartLayoutDetails($chartDetail);

                                            break;
                                        case Axis::AXIS_TYPE_CATEGORY:
                                        case Axis::AXIS_TYPE_DATE:
                                            $catAxRead = true;
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = $this->chartTitle($chartDetail->title->children($this->cNamespace));
                                            }
                                            $xAxis->setAxisType($chartDetailKey);
                                            $this->readEffects($chartDetail, $xAxis);
                                            if (isset($chartDetail->spPr)) {
                                                $sppr = $chartDetail->spPr->children($this->aNamespace);
                                                if (isset($sppr->solidFill)) {
                                                    $axisColorArray = $this->readColor($sppr->solidFill);
                                                    $xAxis->setFillParameters($axisColorArray['value'], $axisColorArray['alpha'], $axisColorArray['type']);
                                                }
                                            }
                                            if (isset($chartDetail->majorGridlines)) {
                                                $majorGridlines = new GridLines();
                                                if (isset($chartDetail->majorGridlines->spPr)) {
                                                    $this->readEffects($chartDetail->majorGridlines, $majorGridlines);
                                                    $this->readLineStyle($chartDetail->majorGridlines, $majorGridlines);
                                                }
                                                $xAxis->setMajorGridlines($majorGridlines);
                                            }
                                            if (isset($chartDetail->minorGridlines)) {
                                                $minorGridlines = new GridLines();
                                                $minorGridlines->activateObject();
                                                if (isset($chartDetail->minorGridlines->spPr)) {
                                                    $this->readEffects($chartDetail->minorGridlines, $minorGridlines);
                                                    $this->readLineStyle($chartDetail->minorGridlines, $minorGridlines);
                                                }
                                                $xAxis->setMinorGridlines($minorGridlines);
                                            }
                                            $this->setAxisProperties($chartDetail, $xAxis);

                                            break;
                                        case Axis::AXIS_TYPE_VALUE:
                                            $whichAxis = null;
                                            $axPos = null;
                                            if (isset($chartDetail->axPos)) {
                                                $axPos = self::getAttribute($chartDetail->axPos, 'val', 'string');
                                            }
                                            if ($catAxRead) {
                                                $whichAxis = $yAxis;
                                                $yAxis->setAxisType($chartDetailKey);
                                            } elseif (!empty($axPos)) {
                                                switch ($axPos) {
                                                    case 't':
                                                    case 'b':
                                                        $whichAxis = $xAxis;
                                                        $xAxis->setAxisType($chartDetailKey);

                                                        break;
                                                    case 'r':
                                                    case 'l':
                                                        $whichAxis = $yAxis;
                                                        $yAxis->setAxisType($chartDetailKey);

                                                        break;
                                                }
                                            }
                                            if (isset($chartDetail->title)) {
                                                $axisLabel = $this->chartTitle($chartDetail->title->children($this->cNamespace));

                                                switch ($axPos) {
                                                    case 't':
                                                    case 'b':
                                                        $XaxisLabel = $axisLabel;

                                                        break;
                                                    case 'r':
                                                    case 'l':
                                                        $YaxisLabel = $axisLabel;

                                                        break;
                                                }
                                            }
                                            $this->readEffects($chartDetail, $whichAxis);
                                            if ($whichAxis !== null && isset($chartDetail->spPr)) {
                                                $sppr = $chartDetail->spPr->children($this->aNamespace);
                                                if (isset($sppr->solidFill)) {
                                                    $axisColorArray = $this->readColor($sppr->solidFill);
                                                    $whichAxis->setFillParameters($axisColorArray['value'], $axisColorArray['alpha'], $axisColorArray['type']);
                                                }
                                            }
                                            if ($whichAxis !== null && isset($chartDetail->majorGridlines)) {
                                                $majorGridlines = new GridLines();
                                                if (isset($chartDetail->majorGridlines->spPr)) {
                                                    $this->readEffects($chartDetail->majorGridlines, $majorGridlines);
                                                    $this->readLineStyle($chartDetail->majorGridlines, $majorGridlines);
                                                }
                                                $whichAxis->setMajorGridlines($majorGridlines);
                                            }
                                            if ($whichAxis !== null && isset($chartDetail->minorGridlines)) {
                                                $minorGridlines = new GridLines();
                                                $minorGridlines->activateObject();
                                                if (isset($chartDetail->minorGridlines->spPr)) {
                                                    $this->readEffects($chartDetail->minorGridlines, $minorGridlines);
                                                    $this->readLineStyle($chartDetail->minorGridlines, $minorGridlines);
                                                }
                                                $whichAxis->setMinorGridlines($minorGridlines);
                                            }
                                            $this->setAxisProperties($chartDetail, $whichAxis);

                                            break;
                                        case 'barChart':
                                        case 'bar3DChart':
                                            $barDirection = self::getAttribute($chartDetail->barDir, 'val', 'string');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotDirection("$barDirection");
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'lineChart':
                                        case 'line3DChart':
                                            $plotSeries[] = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'areaChart':
                                        case 'area3DChart':
                                            $plotSeries[] = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'doughnutChart':
                                        case 'pieChart':
                                        case 'pie3DChart':
                                            $explosion = self::getAttribute($chartDetail->ser->explosion, 'val', 'string');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotStyle("$explosion");
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'scatterChart':
                                            /** @var string */
                                            $scatterStyle = self::getAttribute($chartDetail->scatterStyle, 'val', 'string');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotStyle($scatterStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'bubbleChart':
                                            $bubbleScale = self::getAttribute($chartDetail->bubbleScale, 'val', 'integer');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotStyle("$bubbleScale");
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'radarChart':
                                            /** @var string */
                                            $radarStyle = self::getAttribute($chartDetail->radarStyle, 'val', 'string');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotStyle($radarStyle);
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'surfaceChart':
                                        case 'surface3DChart':
                                            $wireFrame = self::getAttribute($chartDetail->wireframe, 'val', 'boolean');
                                            $plotSer = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotSer->setPlotStyle("$wireFrame");
                                            $plotSeries[] = $plotSer;
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                        case 'stockChart':
                                            $plotSeries[] = $this->chartDataSeries($chartDetail, $chartDetailKey);
                                            $plotAttributes = $this->readChartAttributes($chartDetail);

                                            break;
                                    }
                                }
                                if ($plotAreaLayout == null) {
                                    $plotAreaLayout = new Layout();
                                }
                                $plotArea = new PlotArea($plotAreaLayout, $plotSeries);
                                $this->setChartAttributes($plotAreaLayout, $plotAttributes);
                                if ($plotNoFill) {
                                    $plotArea->setNoFill(true);
                                }
                                if (!empty($gradientArray)) {
                                    $plotArea->setGradientFillProperties($gradientArray, $gradientLin);
                                }

                                break;
                            case 'plotVisOnly':
                                $plotVisOnly = self::getAttribute($chartDetails, 'val', 'string');

                                break;
                            case 'dispBlanksAs':
                                $dispBlanksAs = self::getAttribute($chartDetails, 'val', 'string');

                                break;
                            case 'title':
                                $title = $this->chartTitle($chartDetails);

                                break;
                            case 'legend':
                                $legendPos = 'r';
                                $legendLayout = null;
                                $legendOverlay = false;
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    $chartDetail = Xlsx::testSimpleXml($chartDetail);
                                    switch ($chartDetailKey) {
                                        case 'legendPos':
                                            $legendPos = self::getAttribute($chartDetail, 'val', 'string');

                                            break;
                                        case 'overlay':
                                            $legendOverlay = self::getAttribute($chartDetail, 'val', 'boolean');

                                            break;
                                        case 'layout':
                                            $legendLayout = $this->chartLayoutDetails($chartDetail);

                                            break;
                                    }
                                }
                                $legend = new Legend("$legendPos", $legendLayout, (bool) $legendOverlay);

                                break;
                        }
                    }
            }
        }
        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart($chartName, $title, $legend, $plotArea, $plotVisOnly, (string) $dispBlanksAs, $XaxisLabel, $YaxisLabel, $xAxis, $yAxis);
        if ($chartNoFill) {
            $chart->setNoFill(true);
        }
        $chart->setRoundedCorners($roundedCorners);
        if (is_bool($autoTitleDeleted)) {
            $chart->setAutoTitleDeleted($autoTitleDeleted);
        }
        if (is_int($rotX)) {
            $chart->setRotX($rotX);
        }
        if (is_int($rotY)) {
            $chart->setRotY($rotY);
        }
        if (is_int($rAngAx)) {
            $chart->setRAngAx($rAngAx);
        }
        if (is_int($perspective)) {
            $chart->setPerspective($perspective);
        }

        return $chart;
    }

    private function chartTitle(SimpleXMLElement $titleDetails): Title
    {
        $caption = [];
        $titleLayout = null;
        foreach ($titleDetails as $titleDetailKey => $chartDetail) {
            $chartDetail = Xlsx::testSimpleXml($chartDetail);
            switch ($titleDetailKey) {
                case 'tx':
                    if (isset($chartDetail->rich)) {
                        $titleDetails = $chartDetail->rich->children($this->aNamespace);
                        foreach ($titleDetails as $titleKey => $titleDetail) {
                            $titleDetail = Xlsx::testSimpleXml($titleDetail);
                            switch ($titleKey) {
                                case 'p':
                                    $titleDetailPart = $titleDetail->children($this->aNamespace);
                                    $caption[] = $this->parseRichText($titleDetailPart);
                            }
                        }
                    } elseif (isset($chartDetail->strRef->strCache)) {
                        foreach ($chartDetail->strRef->strCache->pt as $pt) {
                            if (isset($pt->v)) {
                                $caption[] = (string) $pt->v;
                            }
                        }
                    }

                    break;
                case 'layout':
                    $titleLayout = $this->chartLayoutDetails($chartDetail);

                    break;
            }
        }

        return new Title($caption, $titleLayout);
    }

    private function chartLayoutDetails(SimpleXMLElement $chartDetail): ?Layout
    {
        if (!isset($chartDetail->manualLayout)) {
            return null;
        }
        $details = $chartDetail->manualLayout->children($this->cNamespace);
        if ($details === null) {
            return null;
        }
        $layout = [];
        foreach ($details as $detailKey => $detail) {
            $detail = Xlsx::testSimpleXml($detail);
            $layout[$detailKey] = self::getAttribute($detail, 'val', 'string');
        }

        return new Layout($layout);
    }

    private function chartDataSeries(SimpleXMLElement $chartDetail, string $plotType): DataSeries
    {
        $multiSeriesType = null;
        $smoothLine = false;
        $seriesLabel = $seriesCategory = $seriesValues = $plotOrder = $seriesBubbles = [];

        $seriesDetailSet = $chartDetail->children($this->cNamespace);
        foreach ($seriesDetailSet as $seriesDetailKey => $seriesDetails) {
            switch ($seriesDetailKey) {
                case 'grouping':
                    $multiSeriesType = self::getAttribute($chartDetail->grouping, 'val', 'string');

                    break;
                case 'ser':
                    $marker = null;
                    $seriesIndex = '';
                    $fillColor = null;
                    $pointSize = null;
                    $noFill = false;
                    $bubble3D = false;
                    $dptColors = [];
                    $markerFillColor = null;
                    $markerBorderColor = null;
                    $lineStyle = null;
                    $labelLayout = null;
                    $trendLines = [];
                    foreach ($seriesDetails as $seriesKey => $seriesDetail) {
                        $seriesDetail = Xlsx::testSimpleXml($seriesDetail);
                        switch ($seriesKey) {
                            case 'idx':
                                $seriesIndex = self::getAttribute($seriesDetail, 'val', 'integer');

                                break;
                            case 'order':
                                $seriesOrder = self::getAttribute($seriesDetail, 'val', 'integer');
                                $plotOrder[$seriesIndex] = $seriesOrder;

                                break;
                            case 'tx':
                                $seriesLabel[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail);

                                break;
                            case 'spPr':
                                $children = $seriesDetail->children($this->aNamespace);
                                if (isset($children->ln)) {
                                    $ln = $children->ln;
                                    if (is_countable($ln->noFill) && count($ln->noFill) === 1) {
                                        $noFill = true;
                                    }
                                    $lineStyle = new GridLines();
                                    $this->readLineStyle($seriesDetails, $lineStyle);
                                }
                                if (isset($children->effectLst)) {
                                    if ($lineStyle === null) {
                                        $lineStyle = new GridLines();
                                    }
                                    $this->readEffects($seriesDetails, $lineStyle);
                                }
                                if (isset($children->solidFill)) {
                                    $fillColor = new ChartColor($this->readColor($children->solidFill));
                                }

                                break;
                            case 'dPt':
                                $dptIdx = (int) self::getAttribute($seriesDetail->idx, 'val', 'string');
                                if (isset($seriesDetail->spPr)) {
                                    $children = $seriesDetail->spPr->children($this->aNamespace);
                                    if (isset($children->solidFill)) {
                                        $arrayColors = $this->readColor($children->solidFill);
                                        $dptColors[$dptIdx] = new ChartColor($arrayColors);
                                    }
                                }

                                break;
                            case 'trendline':
                                $trendLine = new TrendLine();
                                $this->readLineStyle($seriesDetail, $trendLine);
                                /** @var ?string */
                                $trendLineType = self::getAttribute($seriesDetail->trendlineType, 'val', 'string');
                                /** @var ?bool */
                                $dispRSqr = self::getAttribute($seriesDetail->dispRSqr, 'val', 'boolean');
                                /** @var ?bool */
                                $dispEq = self::getAttribute($seriesDetail->dispEq, 'val', 'boolean');
                                /** @var ?int */
                                $order = self::getAttribute($seriesDetail->order, 'val', 'integer');
                                /** @var ?int */
                                $period = self::getAttribute($seriesDetail->period, 'val', 'integer');
                                /** @var ?float */
                                $forward = self::getAttribute($seriesDetail->forward, 'val', 'float');
                                /** @var ?float */
                                $backward = self::getAttribute($seriesDetail->backward, 'val', 'float');
                                /** @var ?float */
                                $intercept = self::getAttribute($seriesDetail->intercept, 'val', 'float');
                                /** @var ?string */
                                $name = (string) $seriesDetail->name;
                                $trendLine->setTrendLineProperties(
                                    $trendLineType,
                                    $order,
                                    $period,
                                    $dispRSqr,
                                    $dispEq,
                                    $backward,
                                    $forward,
                                    $intercept,
                                    $name
                                );
                                $trendLines[] = $trendLine;

                                break;
                            case 'marker':
                                $marker = self::getAttribute($seriesDetail->symbol, 'val', 'string');
                                $pointSize = self::getAttribute($seriesDetail->size, 'val', 'string');
                                $pointSize = is_numeric($pointSize) ? ((int) $pointSize) : null;
                                if (isset($seriesDetail->spPr)) {
                                    $children = $seriesDetail->spPr->children($this->aNamespace);
                                    if (isset($children->solidFill)) {
                                        $markerFillColor = $this->readColor($children->solidFill);
                                    }
                                    if (isset($children->ln->solidFill)) {
                                        $markerBorderColor = $this->readColor($children->ln->solidFill);
                                    }
                                }

                                break;
                            case 'smooth':
                                $smoothLine = self::getAttribute($seriesDetail, 'val', 'boolean');

                                break;
                            case 'cat':
                                $seriesCategory[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail);

                                break;
                            case 'val':
                                $seriesValues[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", $fillColor, "$pointSize");

                                break;
                            case 'xVal':
                                $seriesCategory[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", $fillColor, "$pointSize");

                                break;
                            case 'yVal':
                                $seriesValues[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", $fillColor, "$pointSize");

                                break;
                            case 'bubbleSize':
                                $seriesBubbles[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", $fillColor, "$pointSize");

                                break;
                            case 'bubble3D':
                                $bubble3D = self::getAttribute($seriesDetail, 'val', 'boolean');

                                break;
                            case 'dLbls':
                                $labelLayout = new Layout($this->readChartAttributes($seriesDetails));

                                break;
                        }
                    }
                    if ($labelLayout) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setLabelLayout($labelLayout);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setLabelLayout($labelLayout);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setLabelLayout($labelLayout);
                        }
                    }
                    if ($noFill) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setScatterLines(false);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setScatterLines(false);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setScatterLines(false);
                        }
                    }
                    if ($lineStyle !== null) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->copyLineStyles($lineStyle);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->copyLineStyles($lineStyle);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->copyLineStyles($lineStyle);
                        }
                    }
                    if ($bubble3D) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setBubble3D($bubble3D);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setBubble3D($bubble3D);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setBubble3D($bubble3D);
                        }
                    }
                    if (!empty($dptColors)) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setFillColor($dptColors);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setFillColor($dptColors);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setFillColor($dptColors);
                        }
                    }
                    if ($markerFillColor !== null) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->getMarkerFillColor()->setColorPropertiesArray($markerFillColor);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->getMarkerFillColor()->setColorPropertiesArray($markerFillColor);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->getMarkerFillColor()->setColorPropertiesArray($markerFillColor);
                        }
                    }
                    if ($markerBorderColor !== null) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->getMarkerBorderColor()->setColorPropertiesArray($markerBorderColor);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->getMarkerBorderColor()->setColorPropertiesArray($markerBorderColor);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->getMarkerBorderColor()->setColorPropertiesArray($markerBorderColor);
                        }
                    }
                    if ($smoothLine) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setSmoothLine(true);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setSmoothLine(true);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setSmoothLine(true);
                        }
                    }
                    if (!empty($trendLines)) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setTrendLines($trendLines);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setTrendLines($trendLines);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setTrendLines($trendLines);
                        }
                    }
            }
        }
        /** @phpstan-ignore-next-line */
        $series = new DataSeries($plotType, $multiSeriesType, $plotOrder, $seriesLabel, $seriesCategory, $seriesValues, $smoothLine);
        $series->setPlotBubbleSizes($seriesBubbles);

        return $series;
    }

    /**
     * @return mixed
     */
    private function chartDataSeriesValueSet(SimpleXMLElement $seriesDetail, ?string $marker = null, ?ChartColor $fillColor = null, ?string $pointSize = null)
    {
        if (isset($seriesDetail->strRef)) {
            $seriesSource = (string) $seriesDetail->strRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $fillColor, "$pointSize");

            if (isset($seriesDetail->strRef->strCache)) {
                $seriesData = $this->chartDataSeriesValues($seriesDetail->strRef->strCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->numRef)) {
            $seriesSource = (string) $seriesDetail->numRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $seriesSource, null, 0, null, $marker, $fillColor, "$pointSize");
            if (isset($seriesDetail->numRef->numCache)) {
                $seriesData = $this->chartDataSeriesValues($seriesDetail->numRef->numCache->children($this->cNamespace));
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlStrRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlStrRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $fillColor, "$pointSize");

            if (isset($seriesDetail->multiLvlStrRef->multiLvlStrCache)) {
                $seriesData = $this->chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlStrRef->multiLvlStrCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlNumRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlNumRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $fillColor, "$pointSize");

            if (isset($seriesDetail->multiLvlNumRef->multiLvlNumCache)) {
                $seriesData = $this->chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlNumRef->multiLvlNumCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        }

        if (isset($seriesDetail->v)) {
            return new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                null,
                null,
                1,
                [(string) $seriesDetail->v]
            );
        }

        return null;
    }

    private function chartDataSeriesValues(SimpleXMLElement $seriesValueSet, string $dataType = 'n'): array
    {
        $seriesVal = [];
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet as $seriesValueIdx => $seriesValue) {
            $seriesValue = Xlsx::testSimpleXml($seriesValue);
            switch ($seriesValueIdx) {
                case 'ptCount':
                    $pointCount = self::getAttribute($seriesValue, 'val', 'integer');

                    break;
                case 'formatCode':
                    $formatCode = (string) $seriesValue;

                    break;
                case 'pt':
                    $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                    if ($dataType == 's') {
                        $seriesVal[$pointVal] = (string) $seriesValue->v;
                    } elseif ((string) $seriesValue->v === ExcelError::NA()) {
                        $seriesVal[$pointVal] = null;
                    } else {
                        $seriesVal[$pointVal] = (float) $seriesValue->v;
                    }

                    break;
            }
        }

        return [
            'formatCode' => $formatCode,
            'pointCount' => $pointCount,
            'dataValues' => $seriesVal,
        ];
    }

    private function chartDataSeriesValuesMultiLevel(SimpleXMLElement $seriesValueSet, string $dataType = 'n'): array
    {
        $seriesVal = [];
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet->lvl as $seriesLevelIdx => $seriesLevel) {
            foreach ($seriesLevel as $seriesValueIdx => $seriesValue) {
                switch ($seriesValueIdx) {
                    case 'ptCount':
                        $pointCount = self::getAttribute($seriesValue, 'val', 'integer');

                        break;
                    case 'formatCode':
                        $formatCode = (string) $seriesValue;

                        break;
                    case 'pt':
                        $pointVal = self::getAttribute($seriesValue, 'idx', 'integer');
                        if ($dataType == 's') {
                            $seriesVal[$pointVal][] = (string) $seriesValue->v;
                        } elseif ((string) $seriesValue->v === ExcelError::NA()) {
                            $seriesVal[$pointVal] = null;
                        } else {
                            $seriesVal[$pointVal][] = (float) $seriesValue->v;
                        }

                        break;
                }
            }
        }

        return [
            'formatCode' => $formatCode,
            'pointCount' => $pointCount,
            'dataValues' => $seriesVal,
        ];
    }

    private function parseRichText(SimpleXMLElement $titleDetailPart): RichText
    {
        $value = new RichText();
        $defaultFontSize = null;
        $defaultBold = null;
        $defaultItalic = null;
        $defaultUnderscore = null;
        $defaultStrikethrough = null;
        $defaultBaseline = null;
        $defaultFontName = null;
        $defaultLatin = null;
        $defaultEastAsian = null;
        $defaultComplexScript = null;
        $defaultFontColor = null;
        if (isset($titleDetailPart->pPr->defRPr)) {
            /** @var ?int */
            $defaultFontSize = self::getAttribute($titleDetailPart->pPr->defRPr, 'sz', 'integer');
            /** @var ?bool */
            $defaultBold = self::getAttribute($titleDetailPart->pPr->defRPr, 'b', 'boolean');
            /** @var ?bool */
            $defaultItalic = self::getAttribute($titleDetailPart->pPr->defRPr, 'i', 'boolean');
            /** @var ?string */
            $defaultUnderscore = self::getAttribute($titleDetailPart->pPr->defRPr, 'u', 'string');
            /** @var ?string */
            $defaultStrikethrough = self::getAttribute($titleDetailPart->pPr->defRPr, 'strike', 'string');
            /** @var ?int */
            $defaultBaseline = self::getAttribute($titleDetailPart->pPr->defRPr, 'baseline', 'integer');
            if (isset($titleDetailPart->defRPr->rFont['val'])) {
                $defaultFontName = (string) $titleDetailPart->defRPr->rFont['val'];
            }
            if (isset($titleDetailPart->pPr->defRPr->latin)) {
                /** @var ?string */
                $defaultLatin = self::getAttribute($titleDetailPart->pPr->defRPr->latin, 'typeface', 'string');
            }
            if (isset($titleDetailPart->pPr->defRPr->ea)) {
                /** @var ?string */
                $defaultEastAsian = self::getAttribute($titleDetailPart->pPr->defRPr->ea, 'typeface', 'string');
            }
            if (isset($titleDetailPart->pPr->defRPr->cs)) {
                /** @var ?string */
                $defaultComplexScript = self::getAttribute($titleDetailPart->pPr->defRPr->cs, 'typeface', 'string');
            }
            if (isset($titleDetailPart->pPr->defRPr->solidFill)) {
                $defaultFontColor = $this->readColor($titleDetailPart->pPr->defRPr->solidFill);
            }
        }
        foreach ($titleDetailPart as $titleDetailElementKey => $titleDetailElement) {
            if (
                (string) $titleDetailElementKey !== 'r'
                || !isset($titleDetailElement->t)
            ) {
                continue;
            }
            $objText = $value->createTextRun((string) $titleDetailElement->t);
            if ($objText->getFont() === null) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }
            $fontSize = null;
            $bold = null;
            $italic = null;
            $underscore = null;
            $strikethrough = null;
            $baseline = null;
            $fontName = null;
            $latinName = null;
            $eastAsian = null;
            $complexScript = null;
            $fontColor = null;
            $underlineColor = null;
            if (isset($titleDetailElement->rPr)) {
                // not used now, not sure it ever was, grandfathering
                if (isset($titleDetailElement->rPr->rFont['val'])) {
                    // @codeCoverageIgnoreStart
                    $fontName = (string) $titleDetailElement->rPr->rFont['val'];
                    // @codeCoverageIgnoreEnd
                }
                if (isset($titleDetailElement->rPr->latin)) {
                    /** @var ?string */
                    $latinName = self::getAttribute($titleDetailElement->rPr->latin, 'typeface', 'string');
                }
                if (isset($titleDetailElement->rPr->ea)) {
                    /** @var ?string */
                    $eastAsian = self::getAttribute($titleDetailElement->rPr->ea, 'typeface', 'string');
                }
                if (isset($titleDetailElement->rPr->cs)) {
                    /** @var ?string */
                    $complexScript = self::getAttribute($titleDetailElement->rPr->cs, 'typeface', 'string');
                }
                /** @var ?int */
                $fontSize = self::getAttribute($titleDetailElement->rPr, 'sz', 'integer');

                // not used now, not sure it ever was, grandfathering
                if (isset($titleDetailElement->rPr->solidFill)) {
                    $fontColor = $this->readColor($titleDetailElement->rPr->solidFill);
                }

                /** @var ?bool */
                $bold = self::getAttribute($titleDetailElement->rPr, 'b', 'boolean');

                /** @var ?bool */
                $italic = self::getAttribute($titleDetailElement->rPr, 'i', 'boolean');

                /** @var ?int */
                $baseline = self::getAttribute($titleDetailElement->rPr, 'baseline', 'integer');

                /** @var ?string */
                $underscore = self::getAttribute($titleDetailElement->rPr, 'u', 'string');
                if (isset($titleDetailElement->rPr->uFill->solidFill)) {
                    $underlineColor = $this->readColor($titleDetailElement->rPr->uFill->solidFill);
                }

                /** @var ?string */
                $strikethrough = self::getAttribute($titleDetailElement->rPr, 'strike', 'string');
            }

            $fontFound = false;
            $latinName = $latinName ?? $defaultLatin;
            if ($latinName !== null) {
                $objText->getFont()->setLatin($latinName);
                $fontFound = true;
            }
            $eastAsian = $eastAsian ?? $defaultEastAsian;
            if ($eastAsian !== null) {
                $objText->getFont()->setEastAsian($eastAsian);
                $fontFound = true;
            }
            $complexScript = $complexScript ?? $defaultComplexScript;
            if ($complexScript !== null) {
                $objText->getFont()->setComplexScript($complexScript);
                $fontFound = true;
            }
            $fontName = $fontName ?? $defaultFontName;
            if ($fontName !== null) {
                // @codeCoverageIgnoreStart
                $objText->getFont()->setName($fontName);
                $fontFound = true;
                // @codeCoverageIgnoreEnd
            }

            $fontSize = $fontSize ?? $defaultFontSize;
            if (is_int($fontSize)) {
                $objText->getFont()->setSize(floor($fontSize / 100));
                $fontFound = true;
            } else {
                $objText->getFont()->setSize(null, true);
            }

            $fontColor = $fontColor ?? $defaultFontColor;
            if (!empty($fontColor)) {
                $objText->getFont()->setChartColor($fontColor);
                $fontFound = true;
            }

            $bold = $bold ?? $defaultBold;
            if ($bold !== null) {
                $objText->getFont()->setBold($bold);
                $fontFound = true;
            }

            $italic = $italic ?? $defaultItalic;
            if ($italic !== null) {
                $objText->getFont()->setItalic($italic);
                $fontFound = true;
            }

            $baseline = $baseline ?? $defaultBaseline;
            if ($baseline !== null) {
                $objText->getFont()->setBaseLine($baseline);
                if ($baseline > 0) {
                    $objText->getFont()->setSuperscript(true);
                } elseif ($baseline < 0) {
                    $objText->getFont()->setSubscript(true);
                }
                $fontFound = true;
            }

            $underscore = $underscore ?? $defaultUnderscore;
            if ($underscore !== null) {
                if ($underscore == 'sng') {
                    $objText->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
                } elseif ($underscore == 'dbl') {
                    $objText->getFont()->setUnderline(Font::UNDERLINE_DOUBLE);
                } elseif ($underscore !== '') {
                    $objText->getFont()->setUnderline($underscore);
                } else {
                    $objText->getFont()->setUnderline(Font::UNDERLINE_NONE);
                }
                $fontFound = true;
                if ($underlineColor) {
                    $objText->getFont()->setUnderlineColor($underlineColor);
                }
            }

            $strikethrough = $strikethrough ?? $defaultStrikethrough;
            if ($strikethrough !== null) {
                $objText->getFont()->setStrikeType($strikethrough);
                if ($strikethrough == 'noStrike') {
                    $objText->getFont()->setStrikethrough(false);
                } else {
                    $objText->getFont()->setStrikethrough(true);
                }
                $fontFound = true;
            }
            if ($fontFound === false) {
                $objText->setFont(null);
            }
        }

        return $value;
    }

    /**
     * @param ?SimpleXMLElement $chartDetail
     */
    private function readChartAttributes($chartDetail): array
    {
        $plotAttributes = [];
        if (isset($chartDetail->dLbls)) {
            if (isset($chartDetail->dLbls->dLblPos)) {
                $plotAttributes['dLblPos'] = self::getAttribute($chartDetail->dLbls->dLblPos, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->numFmt)) {
                $plotAttributes['numFmtCode'] = self::getAttribute($chartDetail->dLbls->numFmt, 'formatCode', 'string');
                $plotAttributes['numFmtLinked'] = self::getAttribute($chartDetail->dLbls->numFmt, 'sourceLinked', 'boolean');
            }
            if (isset($chartDetail->dLbls->showLegendKey)) {
                $plotAttributes['showLegendKey'] = self::getAttribute($chartDetail->dLbls->showLegendKey, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showVal)) {
                $plotAttributes['showVal'] = self::getAttribute($chartDetail->dLbls->showVal, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showCatName)) {
                $plotAttributes['showCatName'] = self::getAttribute($chartDetail->dLbls->showCatName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showSerName)) {
                $plotAttributes['showSerName'] = self::getAttribute($chartDetail->dLbls->showSerName, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showPercent)) {
                $plotAttributes['showPercent'] = self::getAttribute($chartDetail->dLbls->showPercent, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showBubbleSize)) {
                $plotAttributes['showBubbleSize'] = self::getAttribute($chartDetail->dLbls->showBubbleSize, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->showLeaderLines)) {
                $plotAttributes['showLeaderLines'] = self::getAttribute($chartDetail->dLbls->showLeaderLines, 'val', 'string');
            }
            if (isset($chartDetail->dLbls->spPr)) {
                $sppr = $chartDetail->dLbls->spPr->children($this->aNamespace);
                if (isset($sppr->solidFill)) {
                    $plotAttributes['labelFillColor'] = new ChartColor($this->readColor($sppr->solidFill));
                }
                if (isset($sppr->ln->solidFill)) {
                    $plotAttributes['labelBorderColor'] = new ChartColor($this->readColor($sppr->ln->solidFill));
                }
            }
            if (isset($chartDetail->dLbls->txPr)) {
                $txpr = $chartDetail->dLbls->txPr->children($this->aNamespace);
                if (isset($txpr->p->pPr->defRPr->solidFill)) {
                    $plotAttributes['labelFontColor'] = new ChartColor($this->readColor($txpr->p->pPr->defRPr->solidFill));
                }
            }
        }

        return $plotAttributes;
    }

    /**
     * @param mixed $plotAttributes
     */
    private function setChartAttributes(Layout $plotArea, $plotAttributes): void
    {
        foreach ($plotAttributes as $plotAttributeKey => $plotAttributeValue) {
            switch ($plotAttributeKey) {
                case 'showLegendKey':
                    $plotArea->setShowLegendKey($plotAttributeValue);

                    break;
                case 'showVal':
                    $plotArea->setShowVal($plotAttributeValue);

                    break;
                case 'showCatName':
                    $plotArea->setShowCatName($plotAttributeValue);

                    break;
                case 'showSerName':
                    $plotArea->setShowSerName($plotAttributeValue);

                    break;
                case 'showPercent':
                    $plotArea->setShowPercent($plotAttributeValue);

                    break;
                case 'showBubbleSize':
                    $plotArea->setShowBubbleSize($plotAttributeValue);

                    break;
                case 'showLeaderLines':
                    $plotArea->setShowLeaderLines($plotAttributeValue);

                    break;
            }
        }
    }

    private function readEffects(SimpleXMLElement $chartDetail, ?ChartProperties $chartObject): void
    {
        if (!isset($chartObject, $chartDetail->spPr)) {
            return;
        }
        $sppr = $chartDetail->spPr->children($this->aNamespace);

        if (isset($sppr->effectLst->glow)) {
            $axisGlowSize = (float) self::getAttribute($sppr->effectLst->glow, 'rad', 'integer') / ChartProperties::POINTS_WIDTH_MULTIPLIER;
            if ($axisGlowSize != 0.0) {
                $colorArray = $this->readColor($sppr->effectLst->glow);
                $chartObject->setGlowProperties($axisGlowSize, $colorArray['value'], $colorArray['alpha'], $colorArray['type']);
            }
        }

        if (isset($sppr->effectLst->softEdge)) {
            /** @var string */
            $softEdgeSize = self::getAttribute($sppr->effectLst->softEdge, 'rad', 'string');
            if (is_numeric($softEdgeSize)) {
                $chartObject->setSoftEdges((float) ChartProperties::xmlToPoints($softEdgeSize));
            }
        }

        $type = '';
        foreach (self::SHADOW_TYPES as $shadowType) {
            if (isset($sppr->effectLst->$shadowType)) {
                $type = $shadowType;

                break;
            }
        }
        if ($type !== '') {
            /** @var string */
            $blur = self::getAttribute($sppr->effectLst->$type, 'blurRad', 'string');
            $blur = is_numeric($blur) ? ChartProperties::xmlToPoints($blur) : null;
            /** @var string */
            $dist = self::getAttribute($sppr->effectLst->$type, 'dist', 'string');
            $dist = is_numeric($dist) ? ChartProperties::xmlToPoints($dist) : null;
            /** @var string */
            $direction = self::getAttribute($sppr->effectLst->$type, 'dir', 'string');
            $direction = is_numeric($direction) ? ChartProperties::xmlToAngle($direction) : null;
            $algn = self::getAttribute($sppr->effectLst->$type, 'algn', 'string');
            $rot = self::getAttribute($sppr->effectLst->$type, 'rotWithShape', 'string');
            $size = [];
            foreach (['sx', 'sy'] as $sizeType) {
                $sizeValue = self::getAttribute($sppr->effectLst->$type, $sizeType, 'string');
                if (is_numeric($sizeValue)) {
                    $size[$sizeType] = ChartProperties::xmlToTenthOfPercent((string) $sizeValue);
                } else {
                    $size[$sizeType] = null;
                }
            }
            foreach (['kx', 'ky'] as $sizeType) {
                $sizeValue = self::getAttribute($sppr->effectLst->$type, $sizeType, 'string');
                if (is_numeric($sizeValue)) {
                    $size[$sizeType] = ChartProperties::xmlToAngle((string) $sizeValue);
                } else {
                    $size[$sizeType] = null;
                }
            }
            $colorArray = $this->readColor($sppr->effectLst->$type);
            $chartObject
                ->setShadowProperty('effect', $type)
                ->setShadowProperty('blur', $blur)
                ->setShadowProperty('direction', $direction)
                ->setShadowProperty('distance', $dist)
                ->setShadowProperty('algn', $algn)
                ->setShadowProperty('rotWithShape', $rot)
                ->setShadowProperty('size', $size)
                ->setShadowProperty('color', $colorArray);
        }
    }

    private const SHADOW_TYPES = [
        'outerShdw',
        'innerShdw',
    ];

    private function readColor(SimpleXMLElement $colorXml): array
    {
        $result = [
            'type' => null,
            'value' => null,
            'alpha' => null,
            'brightness' => null,
        ];
        foreach (ChartColor::EXCEL_COLOR_TYPES as $type) {
            if (isset($colorXml->$type)) {
                $result['type'] = $type;
                $result['value'] = self::getAttribute($colorXml->$type, 'val', 'string');
                if (isset($colorXml->$type->alpha)) {
                    /** @var string */
                    $alpha = self::getAttribute($colorXml->$type->alpha, 'val', 'string');
                    if (is_numeric($alpha)) {
                        $result['alpha'] = ChartColor::alphaFromXml($alpha);
                    }
                }
                if (isset($colorXml->$type->lumMod)) {
                    /** @var string */
                    $brightness = self::getAttribute($colorXml->$type->lumMod, 'val', 'string');
                    if (is_numeric($brightness)) {
                        $result['brightness'] = ChartColor::alphaFromXml($brightness);
                    }
                }

                break;
            }
        }

        return $result;
    }

    private function readLineStyle(SimpleXMLElement $chartDetail, ?ChartProperties $chartObject): void
    {
        if (!isset($chartObject, $chartDetail->spPr)) {
            return;
        }
        $sppr = $chartDetail->spPr->children($this->aNamespace);

        if (!isset($sppr->ln)) {
            return;
        }
        $lineWidth = null;
        /** @var string */
        $lineWidthTemp = self::getAttribute($sppr->ln, 'w', 'string');
        if (is_numeric($lineWidthTemp)) {
            $lineWidth = ChartProperties::xmlToPoints($lineWidthTemp);
        }
        /** @var string */
        $compoundType = self::getAttribute($sppr->ln, 'cmpd', 'string');
        /** @var string */
        $dashType = self::getAttribute($sppr->ln->prstDash, 'val', 'string');
        /** @var string */
        $capType = self::getAttribute($sppr->ln, 'cap', 'string');
        if (isset($sppr->ln->miter)) {
            $joinType = ChartProperties::LINE_STYLE_JOIN_MITER;
        } elseif (isset($sppr->ln->bevel)) {
            $joinType = ChartProperties::LINE_STYLE_JOIN_BEVEL;
        } else {
            $joinType = '';
        }
        $headArrowSize = '';
        $endArrowSize = '';
        /** @var string */
        $headArrowType = self::getAttribute($sppr->ln->headEnd, 'type', 'string');
        /** @var string */
        $headArrowWidth = self::getAttribute($sppr->ln->headEnd, 'w', 'string');
        /** @var string */
        $headArrowLength = self::getAttribute($sppr->ln->headEnd, 'len', 'string');
        /** @var string */
        $endArrowType = self::getAttribute($sppr->ln->tailEnd, 'type', 'string');
        /** @var string */
        $endArrowWidth = self::getAttribute($sppr->ln->tailEnd, 'w', 'string');
        /** @var string */
        $endArrowLength = self::getAttribute($sppr->ln->tailEnd, 'len', 'string');
        $chartObject->setLineStyleProperties(
            $lineWidth,
            $compoundType,
            $dashType,
            $capType,
            $joinType,
            $headArrowType,
            $headArrowSize,
            $endArrowType,
            $endArrowSize,
            $headArrowWidth,
            $headArrowLength,
            $endArrowWidth,
            $endArrowLength
        );
        $colorArray = $this->readColor($sppr->ln->solidFill);
        $chartObject->getLineColor()->setColorPropertiesArray($colorArray);
    }

    private function setAxisProperties(SimpleXMLElement $chartDetail, ?Axis $whichAxis): void
    {
        if (!isset($whichAxis)) {
            return;
        }
        if (isset($chartDetail->delete)) {
            $whichAxis->setAxisOption('hidden', (string) self::getAttribute($chartDetail->delete, 'val', 'string'));
        }
        if (isset($chartDetail->numFmt)) {
            $whichAxis->setAxisNumberProperties(
                (string) self::getAttribute($chartDetail->numFmt, 'formatCode', 'string'),
                null,
                (int) self::getAttribute($chartDetail->numFmt, 'sourceLinked', 'int')
            );
        }
        if (isset($chartDetail->crossBetween)) {
            $whichAxis->setCrossBetween((string) self::getAttribute($chartDetail->crossBetween, 'val', 'string'));
        }
        if (isset($chartDetail->majorTickMark)) {
            $whichAxis->setAxisOption('major_tick_mark', (string) self::getAttribute($chartDetail->majorTickMark, 'val', 'string'));
        }
        if (isset($chartDetail->minorTickMark)) {
            $whichAxis->setAxisOption('minor_tick_mark', (string) self::getAttribute($chartDetail->minorTickMark, 'val', 'string'));
        }
        if (isset($chartDetail->tickLblPos)) {
            $whichAxis->setAxisOption('axis_labels', (string) self::getAttribute($chartDetail->tickLblPos, 'val', 'string'));
        }
        if (isset($chartDetail->crosses)) {
            $whichAxis->setAxisOption('horizontal_crosses', (string) self::getAttribute($chartDetail->crosses, 'val', 'string'));
        }
        if (isset($chartDetail->crossesAt)) {
            $whichAxis->setAxisOption('horizontal_crosses_value', (string) self::getAttribute($chartDetail->crossesAt, 'val', 'string'));
        }
        if (isset($chartDetail->scaling->orientation)) {
            $whichAxis->setAxisOption('orientation', (string) self::getAttribute($chartDetail->scaling->orientation, 'val', 'string'));
        }
        if (isset($chartDetail->scaling->max)) {
            $whichAxis->setAxisOption('maximum', (string) self::getAttribute($chartDetail->scaling->max, 'val', 'string'));
        }
        if (isset($chartDetail->scaling->min)) {
            $whichAxis->setAxisOption('minimum', (string) self::getAttribute($chartDetail->scaling->min, 'val', 'string'));
        }
        if (isset($chartDetail->scaling->min)) {
            $whichAxis->setAxisOption('minimum', (string) self::getAttribute($chartDetail->scaling->min, 'val', 'string'));
        }
        if (isset($chartDetail->majorUnit)) {
            $whichAxis->setAxisOption('major_unit', (string) self::getAttribute($chartDetail->majorUnit, 'val', 'string'));
        }
        if (isset($chartDetail->minorUnit)) {
            $whichAxis->setAxisOption('minor_unit', (string) self::getAttribute($chartDetail->minorUnit, 'val', 'string'));
        }
        if (isset($chartDetail->baseTimeUnit)) {
            $whichAxis->setAxisOption('baseTimeUnit', (string) self::getAttribute($chartDetail->baseTimeUnit, 'val', 'string'));
        }
        if (isset($chartDetail->majorTimeUnit)) {
            $whichAxis->setAxisOption('majorTimeUnit', (string) self::getAttribute($chartDetail->majorTimeUnit, 'val', 'string'));
        }
        if (isset($chartDetail->minorTimeUnit)) {
            $whichAxis->setAxisOption('minorTimeUnit', (string) self::getAttribute($chartDetail->minorTimeUnit, 'val', 'string'));
        }
        if (isset($chartDetail->txPr)) {
            $children = $chartDetail->txPr->children($this->aNamespace);
            if (isset($children->bodyPr)) {
                /** @var string */
                $textRotation = self::getAttribute($children->bodyPr, 'rot', 'string');
                if (is_numeric($textRotation)) {
                    $whichAxis->setAxisOption('textRotation', (string) ChartProperties::xmlToAngle($textRotation));
                }
            }
        }
    }
}

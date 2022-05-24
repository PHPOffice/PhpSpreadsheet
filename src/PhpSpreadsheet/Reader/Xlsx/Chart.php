<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
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
        foreach ($chartElementsC as $chartElementKey => $chartElement) {
            switch ($chartElementKey) {
                case 'chart':
                    foreach ($chartElement as $chartDetailsKey => $chartDetails) {
                        $chartDetailsC = $chartDetails->children($this->cNamespace);
                        switch ($chartDetailsKey) {
                            case 'view3D':
                                $rotX = self::getAttribute($chartDetails->rotX, 'val', 'integer');
                                $rotY = self::getAttribute($chartDetails->rotY, 'val', 'integer');
                                $rAngAx = self::getAttribute($chartDetails->rAngAx, 'val', 'integer');
                                $perspective = self::getAttribute($chartDetails->perspective, 'val', 'integer');

                                break;
                            case 'plotArea':
                                $plotAreaLayout = $XaxisLabel = $YaxisLabel = null;
                                $plotSeries = $plotAttributes = [];
                                foreach ($chartDetails as $chartDetailKey => $chartDetail) {
                                    switch ($chartDetailKey) {
                                        case 'layout':
                                            $plotAreaLayout = $this->chartLayoutDetails($chartDetail);

                                            break;
                                        case 'catAx':
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = $this->chartTitle($chartDetail->title->children($this->cNamespace));
                                            }

                                            break;
                                        case 'dateAx':
                                            if (isset($chartDetail->title)) {
                                                $XaxisLabel = $this->chartTitle($chartDetail->title->children($this->cNamespace));
                                            }

                                            break;
                                        case 'valAx':
                                            if (isset($chartDetail->title, $chartDetail->axPos)) {
                                                $axisLabel = $this->chartTitle($chartDetail->title->children($this->cNamespace));
                                                $axPos = self::getAttribute($chartDetail->axPos, 'val', 'string');

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
                                            $explosion = isset($chartDetail->ser->explosion);
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
                                            $plotAttributes = $this->readChartAttributes($plotAreaLayout);

                                            break;
                                    }
                                }
                                if ($plotAreaLayout == null) {
                                    $plotAreaLayout = new Layout();
                                }
                                $plotArea = new PlotArea($plotAreaLayout, $plotSeries);
                                $this->setChartAttributes($plotAreaLayout, $plotAttributes);

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
        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart($chartName, $title, $legend, $plotArea, $plotVisOnly, (string) $dispBlanksAs, $XaxisLabel, $YaxisLabel);
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
            switch ($titleDetailKey) {
                case 'tx':
                    $titleDetails = $chartDetail->rich->children($this->aNamespace);
                    foreach ($titleDetails as $titleKey => $titleDetail) {
                        switch ($titleKey) {
                            case 'p':
                                $titleDetailPart = $titleDetail->children($this->aNamespace);
                                $caption[] = $this->parseRichText($titleDetailPart);
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
            $layout[$detailKey] = self::getAttribute($detail, 'val', 'string');
        }

        return new Layout($layout);
    }

    private function chartDataSeries(SimpleXMLElement $chartDetail, string $plotType): DataSeries
    {
        $multiSeriesType = null;
        $smoothLine = false;
        $seriesLabel = $seriesCategory = $seriesValues = $plotOrder = [];

        $seriesDetailSet = $chartDetail->children($this->cNamespace);
        foreach ($seriesDetailSet as $seriesDetailKey => $seriesDetails) {
            switch ($seriesDetailKey) {
                case 'grouping':
                    $multiSeriesType = self::getAttribute($chartDetail->grouping, 'val', 'string');

                    break;
                case 'ser':
                    $marker = null;
                    $seriesIndex = '';
                    $srgbClr = null;
                    $lineWidth = null;
                    $pointSize = null;
                    $noFill = false;
                    $schemeClr = '';
                    $bubble3D = false;
                    foreach ($seriesDetails as $seriesKey => $seriesDetail) {
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
                                $ln = $children->ln;
                                $lineWidth = self::getAttribute($ln, 'w', 'string');
                                if (is_countable($ln->noFill) && count($ln->noFill) === 1) {
                                    $noFill = true;
                                }
                                $sf = $children->solidFill->schemeClr;
                                if ($sf) {
                                    $schemeClr = self::getAttribute($sf, 'val', 'string');
                                }

                                break;
                            case 'marker':
                                $marker = self::getAttribute($seriesDetail->symbol, 'val', 'string');
                                $pointSize = self::getAttribute($seriesDetail->size, 'val', 'string');
                                $pointSize = is_numeric($pointSize) ? ((int) $pointSize) : null;
                                if (count($seriesDetail->spPr) === 1) {
                                    $ln = $seriesDetail->spPr->children($this->aNamespace);
                                    if (count($ln->solidFill) === 1) {
                                        $srgbClr = self::getAttribute($ln->solidFill->srgbClr, 'val', 'string');
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
                                $seriesValues[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", "$srgbClr", "$pointSize");

                                break;
                            case 'xVal':
                                $seriesCategory[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", "$srgbClr", "$pointSize");

                                break;
                            case 'yVal':
                                $seriesValues[$seriesIndex] = $this->chartDataSeriesValueSet($seriesDetail, "$marker", "$srgbClr", "$pointSize");

                                break;
                            case 'bubble3D':
                                $bubble3D = self::getAttribute($seriesDetail, 'val', 'boolean');

                                break;
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
                    if (is_numeric($lineWidth)) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setLineWidth((int) $lineWidth);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setLineWidth((int) $lineWidth);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setLineWidth((int) $lineWidth);
                        }
                    }
                    if ($schemeClr) {
                        if (isset($seriesLabel[$seriesIndex])) {
                            $seriesLabel[$seriesIndex]->setSchemeClr($schemeClr);
                        }
                        if (isset($seriesCategory[$seriesIndex])) {
                            $seriesCategory[$seriesIndex]->setSchemeClr($schemeClr);
                        }
                        if (isset($seriesValues[$seriesIndex])) {
                            $seriesValues[$seriesIndex]->setSchemeClr($schemeClr);
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
            }
        }

        /** @phpstan-ignore-next-line */
        return new DataSeries($plotType, $multiSeriesType, $plotOrder, $seriesLabel, $seriesCategory, $seriesValues, $smoothLine);
    }

    /**
     * @return mixed
     */
    private function chartDataSeriesValueSet(SimpleXMLElement $seriesDetail, ?string $marker = null, ?string $srgbClr = null, ?string $pointSize = null)
    {
        if (isset($seriesDetail->strRef)) {
            $seriesSource = (string) $seriesDetail->strRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $srgbClr, "$pointSize");

            if (isset($seriesDetail->strRef->strCache)) {
                $seriesData = $this->chartDataSeriesValues($seriesDetail->strRef->strCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->numRef)) {
            $seriesSource = (string) $seriesDetail->numRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $seriesSource, null, 0, null, $marker, $srgbClr, "$pointSize");
            if (isset($seriesDetail->numRef->numCache)) {
                $seriesData = $this->chartDataSeriesValues($seriesDetail->numRef->numCache->children($this->cNamespace));
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlStrRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlStrRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $srgbClr, "$pointSize");

            if (isset($seriesDetail->multiLvlStrRef->multiLvlStrCache)) {
                $seriesData = $this->chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlStrRef->multiLvlStrCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        } elseif (isset($seriesDetail->multiLvlNumRef)) {
            $seriesSource = (string) $seriesDetail->multiLvlNumRef->f;
            $seriesValues = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $seriesSource, null, 0, null, $marker, $srgbClr, "$pointSize");

            if (isset($seriesDetail->multiLvlNumRef->multiLvlNumCache)) {
                $seriesData = $this->chartDataSeriesValuesMultiLevel($seriesDetail->multiLvlNumRef->multiLvlNumCache->children($this->cNamespace), 's');
                $seriesValues
                    ->setFormatCode($seriesData['formatCode'])
                    ->setDataValues($seriesData['dataValues']);
            }

            return $seriesValues;
        }

        return null;
    }

    private function chartDataSeriesValues(SimpleXMLElement $seriesValueSet, string $dataType = 'n'): array
    {
        $seriesVal = [];
        $formatCode = '';
        $pointCount = 0;

        foreach ($seriesValueSet as $seriesValueIdx => $seriesValue) {
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
        $objText = null;
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
        $defaultColor = null;
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
            if (isset($titleDetailPart->pPr->defRPr->solidFill->srgbClr)) {
                /** @var ?string */
                $defaultColor = self::getAttribute($titleDetailPart->pPr->defRPr->solidFill->srgbClr, 'val', 'string');
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
            $uSchemeClr = null;
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
                /** @var ?string */
                $fontColor = self::getAttribute($titleDetailElement->rPr, 'color', 'string');
                if (isset($titleDetailElement->rPr->solidFill->srgbClr)) {
                    /** @var ?string */
                    $fontColor = self::getAttribute($titleDetailElement->rPr->solidFill->srgbClr, 'val', 'string');
                }

                /** @var ?bool */
                $bold = self::getAttribute($titleDetailElement->rPr, 'b', 'boolean');

                /** @var ?bool */
                $italic = self::getAttribute($titleDetailElement->rPr, 'i', 'boolean');

                /** @var ?int */
                $baseline = self::getAttribute($titleDetailElement->rPr, 'baseline', 'integer');

                /** @var ?string */
                $underscore = self::getAttribute($titleDetailElement->rPr, 'u', 'string');
                if (isset($titleDetailElement->rPr->uFill->solidFill->schemeClr)) {
                    /** @var ?string */
                    $uSchemeClr = self::getAttribute($titleDetailElement->rPr->uFill->solidFill->schemeClr, 'val', 'string');
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
            }

            $fontColor = $fontColor ?? $defaultColor;
            if ($fontColor !== null) {
                $objText->getFont()->setColor(new Color($fontColor));
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
                if ($uSchemeClr) {
                    $objText->getFont()->setUSchemeClr($uSchemeClr);
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
     * @param null|Layout|SimpleXMLElement $chartDetail
     */
    private function readChartAttributes($chartDetail): array
    {
        $plotAttributes = [];
        if (isset($chartDetail->dLbls)) {
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
}

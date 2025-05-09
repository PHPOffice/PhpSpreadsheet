<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSeriesValues extends Properties
{
    const DATASERIES_TYPE_STRING = 'String';
    const DATASERIES_TYPE_NUMBER = 'Number';

    private const DATA_TYPE_VALUES = [
        self::DATASERIES_TYPE_STRING,
        self::DATASERIES_TYPE_NUMBER,
    ];

    /**
     * Series Data Type.
     */
    private string $dataType;

    /**
     * Series Data Source.
     */
    private ?string $dataSource;

    /**
     * Format Code.
     */
    private ?string $formatCode;

    /**
     * Series Point Marker.
     */
    private ?string $pointMarker;

    private ChartColor $markerFillColor;

    private ChartColor $markerBorderColor;

    /**
     * Series Point Size.
     */
    private int $pointSize = 3;

    /**
     * Point Count (The number of datapoints in the dataseries).
     */
    private int $pointCount;

    /**
     * Data Values.
     *
     * @var null|mixed[]
     */
    private ?array $dataValues;

    /**
     * Fill color (can be array with colors if dataseries have custom colors).
     *
     * @var null|ChartColor|ChartColor[]
     */
    private $fillColor;

    private bool $scatterLines = true;

    private bool $bubble3D = false;

    private ?Layout $labelLayout = null;

    /** @var TrendLine[] */
    private array $trendLines = [];

    /**
     * Create a new DataSeriesValues object.
     *
     * @param null|mixed[] $dataValues
     * @param null|ChartColor|ChartColor[]|string|string[] $fillColor
     */
    public function __construct(
        string $dataType = self::DATASERIES_TYPE_NUMBER,
        ?string $dataSource = null,
        ?string $formatCode = null,
        int $pointCount = 0,
        ?array $dataValues = [],
        ?string $marker = null,
        null|ChartColor|array|string $fillColor = null,
        int|string $pointSize = 3
    ) {
        parent::__construct();
        $this->markerFillColor = new ChartColor();
        $this->markerBorderColor = new ChartColor();
        $this->setDataType($dataType);
        $this->dataSource = $dataSource;
        $this->formatCode = $formatCode;
        $this->pointCount = $pointCount;
        $this->dataValues = $dataValues;
        $this->pointMarker = $marker;
        if ($fillColor !== null) {
            $this->setFillColor($fillColor);
        }
        if (is_numeric($pointSize)) {
            $this->pointSize = (int) $pointSize;
        }
    }

    /**
     * Get Series Data Type.
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * Set Series Data Type.
     *
     * @param string $dataType Datatype of this data series
     *                                Typical values are:
     *                                    DataSeriesValues::DATASERIES_TYPE_STRING
     *                                        Normally used for axis point values
     *                                    DataSeriesValues::DATASERIES_TYPE_NUMBER
     *                                        Normally used for chart data values
     *
     * @return $this
     */
    public function setDataType(string $dataType): static
    {
        if (!in_array($dataType, self::DATA_TYPE_VALUES)) {
            throw new Exception('Invalid datatype for chart data series values');
        }
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Get Series Data Source (formula).
     */
    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    /**
     * Set Series Data Source (formula).
     *
     * @return $this
     */
    public function setDataSource(?string $dataSource): static
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * Get Point Marker.
     */
    public function getPointMarker(): ?string
    {
        return $this->pointMarker;
    }

    /**
     * Set Point Marker.
     *
     * @return $this
     */
    public function setPointMarker(string $marker): static
    {
        $this->pointMarker = $marker;

        return $this;
    }

    public function getMarkerFillColor(): ChartColor
    {
        return $this->markerFillColor;
    }

    public function getMarkerBorderColor(): ChartColor
    {
        return $this->markerBorderColor;
    }

    /**
     * Get Point Size.
     */
    public function getPointSize(): int
    {
        return $this->pointSize;
    }

    /**
     * Set Point Size.
     *
     * @return $this
     */
    public function setPointSize(int $size = 3): static
    {
        $this->pointSize = $size;

        return $this;
    }

    /**
     * Get Series Format Code.
     */
    public function getFormatCode(): ?string
    {
        return $this->formatCode;
    }

    /**
     * Set Series Format Code.
     *
     * @return $this
     */
    public function setFormatCode(string $formatCode): static
    {
        $this->formatCode = $formatCode;

        return $this;
    }

    /**
     * Get Series Point Count.
     */
    public function getPointCount(): int
    {
        return $this->pointCount;
    }

    /**
     * Get fill color object.
     *
     * @return null|ChartColor|ChartColor[]
     */
    public function getFillColorObject()
    {
        return $this->fillColor;
    }

    private function stringToChartColor(string $fillString): ChartColor
    {
        $value = $type = '';
        if (str_starts_with($fillString, '*')) {
            $type = 'schemeClr';
            $value = substr($fillString, 1);
        } elseif (str_starts_with($fillString, '/')) {
            $type = 'prstClr';
            $value = substr($fillString, 1);
        } elseif ($fillString !== '') {
            $type = 'srgbClr';
            $value = $fillString;
            $this->validateColor($value);
        }

        return new ChartColor($value, null, $type);
    }

    private function chartColorToString(ChartColor $chartColor): string
    {
        $type = (string) $chartColor->getColorProperty('type');
        $value = (string) $chartColor->getColorProperty('value');
        if ($type === '' || $value === '') {
            return '';
        }
        if ($type === 'schemeClr') {
            return "*$value";
        }
        if ($type === 'prstClr') {
            return "/$value";
        }

        return $value;
    }

    /**
     * Get fill color.
     *
     * @return string|string[] HEX color or array with HEX colors
     */
    public function getFillColor(): string|array
    {
        if ($this->fillColor === null) {
            return '';
        }
        if (is_array($this->fillColor)) {
            $array = [];
            foreach ($this->fillColor as $chartColor) {
                $array[] = $this->chartColorToString($chartColor);
            }

            return $array;
        }

        return $this->chartColorToString($this->fillColor);
    }

    /**
     * Set fill color for series.
     *
     * @param ChartColor|ChartColor[]|string|string[] $color HEX color or array with HEX colors
     *
     * @return   $this
     */
    public function setFillColor($color): static
    {
        if (is_array($color)) {
            $this->fillColor = [];
            foreach ($color as $fillString) {
                if ($fillString instanceof ChartColor) {
                    $this->fillColor[] = $fillString;
                } else {
                    $this->fillColor[] = $this->stringToChartColor($fillString);
                }
            }
        } elseif ($color instanceof ChartColor) {
            $this->fillColor = $color;
        } else {
            $this->fillColor = $this->stringToChartColor($color);
        }

        return $this;
    }

    /**
     * Method for validating hex color.
     *
     * @param string $color value for color
     *
     * @return bool true if validation was successful
     */
    private function validateColor(string $color): bool
    {
        if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
            throw new Exception(sprintf('Invalid hex color for chart series (color: "%s")', $color));
        }

        return true;
    }

    /**
     * Get line width for series.
     */
    public function getLineWidth(): null|float|int
    {
        /** @var null|float|int */
        $temp = $this->lineStyleProperties['width'];

        return $temp;
    }

    /**
     * Set line width for the series.
     *
     * @return $this
     */
    public function setLineWidth(null|float|int $width): static
    {
        $this->lineStyleProperties['width'] = $width;

        return $this;
    }

    /**
     * Identify if the Data Series is a multi-level or a simple series.
     */
    public function isMultiLevelSeries(): ?bool
    {
        if (!empty($this->dataValues)) {
            return is_array(array_values($this->dataValues)[0]);
        }

        return null;
    }

    /**
     * Return the level count of a multi-level Data Series.
     */
    public function multiLevelCount(): int
    {
        $levelCount = 0;
        foreach (($this->dataValues ?? []) as $dataValueSet) {
            /** @var mixed[] $dataValueSet */
            $levelCount = max($levelCount, count($dataValueSet));
        }

        return $levelCount;
    }

    /**
     * Get Series Data Values.
     *
     * @return null|mixed[]
     */
    public function getDataValues(): ?array
    {
        return $this->dataValues;
    }

    /**
     * Get the first Series Data value.
     */
    public function getDataValue(): mixed
    {
        if ($this->dataValues === null) {
            return null;
        }
        $count = count($this->dataValues);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            return $this->dataValues[0];
        }

        return $this->dataValues;
    }

    /**
     * Set Series Data Values.
     *
     * @param mixed[] $dataValues
     *
     * @return $this
     */
    public function setDataValues(array $dataValues): static
    {
        $this->dataValues = Functions::flattenArray($dataValues);
        $this->pointCount = count($dataValues);

        return $this;
    }

    public function refresh(Worksheet $worksheet, bool $flatten = true): void
    {
        if ($this->dataSource !== null) {
            $calcEngine = Calculation::getInstance($worksheet->getParent());
            $newDataValues = Calculation::unwrapResult(
                $calcEngine->_calculateFormulaValue(
                    '=' . $this->dataSource,
                    null,
                    $worksheet->getCell('A1')
                )
            );
            if ($flatten) {
                $this->dataValues = Functions::flattenArray($newDataValues);
                foreach ($this->dataValues as &$dataValue) {
                    if (is_string($dataValue) && !empty($dataValue) && $dataValue[0] == '#') {
                        $dataValue = 0.0;
                    }
                }
                unset($dataValue);
            } else {
                [, $cellRange] = Worksheet::extractSheetTitle($this->dataSource, true);
                $dimensions = Coordinate::rangeDimension(str_replace('$', '', $cellRange ?? ''));
                if (($dimensions[0] == 1) || ($dimensions[1] == 1)) {
                    $this->dataValues = Functions::flattenArray($newDataValues);
                } else {
                    /** @var array<int, mixed[]> */
                    $newDataValuesx = $newDataValues;
                    /** @var mixed[][] $newArray */
                    $newArray = array_values(array_shift($newDataValuesx) ?? []);
                    foreach ($newArray as $i => $newDataSet) {
                        $newArray[$i] = [$newDataSet];
                    }

                    foreach ($newDataValuesx as $newDataSet) {
                        $i = 0;
                        foreach ($newDataSet as $newDataVal) {
                            array_unshift($newArray[$i++], $newDataVal);
                        }
                    }
                    $this->dataValues = $newArray;
                }
            }
            $this->pointCount = count($this->dataValues ?? []);
        }
    }

    public function getScatterLines(): bool
    {
        return $this->scatterLines;
    }

    public function setScatterLines(bool $scatterLines): self
    {
        $this->scatterLines = $scatterLines;

        return $this;
    }

    public function getBubble3D(): bool
    {
        return $this->bubble3D;
    }

    public function setBubble3D(bool $bubble3D): self
    {
        $this->bubble3D = $bubble3D;

        return $this;
    }

    /**
     * Smooth Line. Must be specified for both DataSeries and DataSeriesValues.
     */
    private bool $smoothLine = false;

    /**
     * Get Smooth Line.
     */
    public function getSmoothLine(): bool
    {
        return $this->smoothLine;
    }

    /**
     * Set Smooth Line.
     *
     * @return $this
     */
    public function setSmoothLine(bool $smoothLine): static
    {
        $this->smoothLine = $smoothLine;

        return $this;
    }

    public function getLabelLayout(): ?Layout
    {
        return $this->labelLayout;
    }

    public function setLabelLayout(?Layout $labelLayout): self
    {
        $this->labelLayout = $labelLayout;

        return $this;
    }

    /** @param TrendLine[] $trendLines */
    public function setTrendLines(array $trendLines): self
    {
        $this->trendLines = $trendLines;

        return $this;
    }

    /** @return TrendLine[] */
    public function getTrendLines(): array
    {
        return $this->trendLines;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        parent::__clone();
        $this->markerFillColor = clone $this->markerFillColor;
        $this->markerBorderColor = clone $this->markerBorderColor;
        if (is_array($this->fillColor)) {
            $fillColor = $this->fillColor;
            $this->fillColor = [];
            foreach ($fillColor as $color) {
                $this->fillColor[] = clone $color;
            }
        } elseif ($this->fillColor instanceof ChartColor) {
            $this->fillColor = clone $this->fillColor;
        }
        $this->labelLayout = ($this->labelLayout === null) ? null : clone $this->labelLayout;
        $trendLines = $this->trendLines;
        $this->trendLines = [];
        foreach ($trendLines as $trendLine) {
            $this->trendLines[] = clone $trendLine;
        }
    }
}

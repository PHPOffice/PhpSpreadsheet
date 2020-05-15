<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSeriesValues
{
    const DATASERIES_TYPE_STRING = 'String';
    const DATASERIES_TYPE_NUMBER = 'Number';

    private static $dataTypeValues = [
        self::DATASERIES_TYPE_STRING,
        self::DATASERIES_TYPE_NUMBER,
    ];

    /**
     * Series Data Type.
     *
     * @var string
     */
    private $dataType;

    /**
     * Series Data Source.
     *
     * @var string
     */
    private $dataSource;

    /**
     * Format Code.
     *
     * @var string
     */
    private $formatCode;

    /**
     * Series Point Marker.
     *
     * @var string
     */
    private $pointMarker;

    /**
     * Point Count (The number of datapoints in the dataseries).
     *
     * @var int
     */
    private $pointCount = 0;

    /**
     * Data Values.
     *
     * @var array of mixed
     */
    private $dataValues = [];

    /**
     * Fill color (can be array with colors if dataseries have custom colors).
     *
     * @var string|string[]
     */
    private $fillColor;

    /**
     * Line Width.
     *
     * @var int
     */
    private $lineWidth = 12700;

    /**
     * Create a new DataSeriesValues object.
     *
     * @param string $dataType
     * @param string $dataSource
     * @param null|mixed $formatCode
     * @param int $pointCount
     * @param mixed $dataValues
     * @param null|mixed $marker
     * @param null|string|string[] $fillColor
     */
    public function __construct($dataType = self::DATASERIES_TYPE_NUMBER, $dataSource = null, $formatCode = null, $pointCount = 0, $dataValues = [], $marker = null, $fillColor = null)
    {
        $this->setDataType($dataType);
        $this->dataSource = $dataSource;
        $this->formatCode = $formatCode;
        $this->pointCount = $pointCount;
        $this->dataValues = $dataValues;
        $this->pointMarker = $marker;
        $this->fillColor = $fillColor;
    }

    /**
     * Get Series Data Type.
     *
     * @return string
     */
    public function getDataType()
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
     * @throws Exception
     *
     * @return DataSeriesValues
     */
    public function setDataType($dataType)
    {
        if (!in_array($dataType, self::$dataTypeValues)) {
            throw new Exception('Invalid datatype for chart data series values');
        }
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Get Series Data Source (formula).
     *
     * @return string
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Set Series Data Source (formula).
     *
     * @param string $dataSource
     *
     * @return DataSeriesValues
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * Get Point Marker.
     *
     * @return string
     */
    public function getPointMarker()
    {
        return $this->pointMarker;
    }

    /**
     * Set Point Marker.
     *
     * @param string $marker
     *
     * @return DataSeriesValues
     */
    public function setPointMarker($marker)
    {
        $this->pointMarker = $marker;

        return $this;
    }

    /**
     * Get Series Format Code.
     *
     * @return string
     */
    public function getFormatCode()
    {
        return $this->formatCode;
    }

    /**
     * Set Series Format Code.
     *
     * @param string $formatCode
     *
     * @return DataSeriesValues
     */
    public function setFormatCode($formatCode)
    {
        $this->formatCode = $formatCode;

        return $this;
    }

    /**
     * Get Series Point Count.
     *
     * @return int
     */
    public function getPointCount()
    {
        return $this->pointCount;
    }

    /**
     * Get fill color.
     *
     * @return string|string[] HEX color or array with HEX colors
     */
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * Set fill color for series.
     *
     * @param string|string[] $color HEX color or array with HEX colors
     *
     * @return   DataSeriesValues
     */
    public function setFillColor($color)
    {
        if (is_array($color)) {
            foreach ($color as $colorValue) {
                $this->validateColor($colorValue);
            }
        } else {
            $this->validateColor($color);
        }
        $this->fillColor = $color;

        return $this;
    }

    /**
     * Method for validating hex color.
     *
     * @param string $color value for color
     *
     * @throws \Exception thrown if color is invalid
     *
     * @return bool true if validation was successful
     */
    private function validateColor($color)
    {
        if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
            throw new Exception(sprintf('Invalid hex color for chart series (color: "%s")', $color));
        }

        return true;
    }

    /**
     * Get line width for series.
     *
     * @return int
     */
    public function getLineWidth()
    {
        return $this->lineWidth;
    }

    /**
     * Set line width for the series.
     *
     * @param int $width
     *
     * @return DataSeriesValues
     */
    public function setLineWidth($width)
    {
        $minWidth = 12700;
        $this->lineWidth = max($minWidth, $width);

        return $this;
    }

    /**
     * Identify if the Data Series is a multi-level or a simple series.
     *
     * @return null|bool
     */
    public function isMultiLevelSeries()
    {
        if (count($this->dataValues) > 0) {
            return is_array(array_values($this->dataValues)[0]);
        }

        return null;
    }

    /**
     * Return the level count of a multi-level Data Series.
     *
     * @return int
     */
    public function multiLevelCount()
    {
        $levelCount = 0;
        foreach ($this->dataValues as $dataValueSet) {
            $levelCount = max($levelCount, count($dataValueSet));
        }

        return $levelCount;
    }

    /**
     * Get Series Data Values.
     *
     * @return array of mixed
     */
    public function getDataValues()
    {
        return $this->dataValues;
    }

    /**
     * Get the first Series Data value.
     *
     * @return mixed
     */
    public function getDataValue()
    {
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
     * @param array $dataValues
     *
     * @return DataSeriesValues
     */
    public function setDataValues($dataValues)
    {
        $this->dataValues = Functions::flattenArray($dataValues);
        $this->pointCount = count($dataValues);

        return $this;
    }

    public function refresh(Worksheet $worksheet, $flatten = true)
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
                [$worksheet, $cellRange] = Worksheet::extractSheetTitle($this->dataSource, true);
                $dimensions = Coordinate::rangeDimension(str_replace('$', '', $cellRange));
                if (($dimensions[0] == 1) || ($dimensions[1] == 1)) {
                    $this->dataValues = Functions::flattenArray($newDataValues);
                } else {
                    $newArray = array_values(array_shift($newDataValues));
                    foreach ($newArray as $i => $newDataSet) {
                        $newArray[$i] = [$newDataSet];
                    }

                    foreach ($newDataValues as $newDataSet) {
                        $i = 0;
                        foreach ($newDataSet as $newDataVal) {
                            array_unshift($newArray[$i++], $newDataVal);
                        }
                    }
                    $this->dataValues = $newArray;
                }
            }
            $this->pointCount = count($this->dataValues);
        }
    }
}

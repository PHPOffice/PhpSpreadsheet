<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 6/17/14
 * Time: 12:11 PM.
 */
class Axis extends Properties
{
    const AXIS_TYPE_CATEGORY = 'catAx';
    const AXIS_TYPE_DATE = 'dateAx';
    const AXIS_TYPE_VALUE = 'valAx';

    const TIME_UNIT_DAYS = 'days';
    const TIME_UNIT_MONTHS = 'months';
    const TIME_UNIT_YEARS = 'years';

    public function __construct()
    {
        parent::__construct();
        $this->fillColor = new ChartColor();
    }

    /**
     * Chart Major Gridlines as.
     *
     * @var ?GridLines
     */
    private $majorGridlines;

    /**
     * Chart Minor Gridlines as.
     *
     * @var ?GridLines
     */
    private $minorGridlines;

    /**
     * Axis Number.
     *
     * @var mixed[]
     */
    private $axisNumber = [
        'format' => self::FORMAT_CODE_GENERAL,
        'source_linked' => 1,
        'numeric' => null,
    ];

    /** @var string */
    private $axisType = '';

    /** @var ?AxisText */
    private $axisText;

    /**
     * Axis Options.
     *
     * @var mixed[]
     */
    private $axisOptions = [
        'minimum' => null,
        'maximum' => null,
        'major_unit' => null,
        'minor_unit' => null,
        'orientation' => self::ORIENTATION_NORMAL,
        'minor_tick_mark' => self::TICK_MARK_NONE,
        'major_tick_mark' => self::TICK_MARK_NONE,
        'axis_labels' => self::AXIS_LABELS_NEXT_TO,
        'horizontal_crosses' => self::HORIZONTAL_CROSSES_AUTOZERO,
        'horizontal_crosses_value' => null,
        'textRotation' => null,
        'hidden' => null,
        'majorTimeUnit' => self::TIME_UNIT_YEARS,
        'minorTimeUnit' => self::TIME_UNIT_MONTHS,
        'baseTimeUnit' => self::TIME_UNIT_DAYS,
    ];

    /**
     * Fill Properties.
     *
     * @var ChartColor
     */
    private $fillColor;

    private const NUMERIC_FORMAT = [
        Properties::FORMAT_CODE_NUMBER,
        Properties::FORMAT_CODE_DATE,
        Properties::FORMAT_CODE_DATE_ISO8601,
    ];

    /** @var bool */
    private $noFill = false;

    /**
     * Get Series Data Type.
     *
     * @param mixed $format_code
     */
    public function setAxisNumberProperties($format_code, ?bool $numeric = null, int $sourceLinked = 0): void
    {
        $format = (string) $format_code;
        $this->axisNumber['format'] = $format;
        $this->axisNumber['source_linked'] = $sourceLinked;
        if (is_bool($numeric)) {
            $this->axisNumber['numeric'] = $numeric;
        } elseif (in_array($format, self::NUMERIC_FORMAT, true)) {
            $this->axisNumber['numeric'] = true;
        }
    }

    /**
     * Get Axis Number Format Data Type.
     *
     * @return string
     */
    public function getAxisNumberFormat()
    {
        return $this->axisNumber['format'];
    }

    /**
     * Get Axis Number Source Linked.
     *
     * @return string
     */
    public function getAxisNumberSourceLinked()
    {
        return (string) $this->axisNumber['source_linked'];
    }

    public function getAxisIsNumericFormat(): bool
    {
        return $this->axisType === self::AXIS_TYPE_DATE || (bool) $this->axisNumber['numeric'];
    }

    public function setAxisOption(string $key, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $this->axisOptions[$key] = $value;
        }
    }

    /**
     * Set Axis Options Properties.
     */
    public function setAxisOptionsProperties(
        string $axisLabels,
        ?string $horizontalCrossesValue = null,
        ?string $horizontalCrosses = null,
        ?string $axisOrientation = null,
        ?string $majorTmt = null,
        ?string $minorTmt = null,
        ?string $minimum = null,
        ?string $maximum = null,
        ?string $majorUnit = null,
        ?string $minorUnit = null,
        ?string $textRotation = null,
        ?string $hidden = null,
        ?string $baseTimeUnit = null,
        ?string $majorTimeUnit = null,
        ?string $minorTimeUnit = null
    ): void {
        $this->axisOptions['axis_labels'] = $axisLabels;
        $this->setAxisOption('horizontal_crosses_value', $horizontalCrossesValue);
        $this->setAxisOption('horizontal_crosses', $horizontalCrosses);
        $this->setAxisOption('orientation', $axisOrientation);
        $this->setAxisOption('major_tick_mark', $majorTmt);
        $this->setAxisOption('minor_tick_mark', $minorTmt);
        $this->setAxisOption('minimum', $minimum);
        $this->setAxisOption('maximum', $maximum);
        $this->setAxisOption('major_unit', $majorUnit);
        $this->setAxisOption('minor_unit', $minorUnit);
        $this->setAxisOption('textRotation', $textRotation);
        $this->setAxisOption('hidden', $hidden);
        $this->setAxisOption('baseTimeUnit', $baseTimeUnit);
        $this->setAxisOption('majorTimeUnit', $majorTimeUnit);
        $this->setAxisOption('minorTimeUnit', $minorTimeUnit);
    }

    /**
     * Get Axis Options Property.
     *
     * @param string $property
     *
     * @return ?string
     */
    public function getAxisOptionsProperty($property)
    {
        if ($property === 'textRotation') {
            if ($this->axisText !== null) {
                if ($this->axisText->getRotation() !== null) {
                    return (string) $this->axisText->getRotation();
                }
            }
        }

        return $this->axisOptions[$property];
    }

    /**
     * Set Axis Orientation Property.
     *
     * @param string $orientation
     */
    public function setAxisOrientation($orientation): void
    {
        $this->axisOptions['orientation'] = (string) $orientation;
    }

    public function getAxisType(): string
    {
        return $this->axisType;
    }

    public function setAxisType(string $type): self
    {
        if ($type === self::AXIS_TYPE_CATEGORY || $type === self::AXIS_TYPE_VALUE || $type === self::AXIS_TYPE_DATE) {
            $this->axisType = $type;
        } else {
            $this->axisType = '';
        }

        return $this;
    }

    /**
     * Set Fill Property.
     *
     * @param ?string $color
     * @param ?int $alpha
     * @param ?string $AlphaType
     */
    public function setFillParameters($color, $alpha = null, $AlphaType = ChartColor::EXCEL_COLOR_TYPE_RGB): void
    {
        $this->fillColor->setColorProperties($color, $alpha, $AlphaType);
    }

    /**
     * Get Fill Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getFillProperty($property)
    {
        return (string) $this->fillColor->getColorProperty($property);
    }

    public function getFillColorObject(): ChartColor
    {
        return $this->fillColor;
    }

    /**
     * Get Line Color Property.
     *
     * @deprecated 1.24.0
     *      Use the getLineColor property in the Properties class instead
     * @see Properties::getLineColorProperty()
     *
     * @param string $propertyName
     *
     * @return null|int|string
     */
    public function getLineProperty($propertyName)
    {
        return $this->getLineColorProperty($propertyName);
    }

    /** @var string */
    private $crossBetween = ''; // 'between' or 'midCat' might be better

    public function setCrossBetween(string $crossBetween): self
    {
        $this->crossBetween = $crossBetween;

        return $this;
    }

    public function getCrossBetween(): string
    {
        return $this->crossBetween;
    }

    public function getMajorGridlines(): ?GridLines
    {
        return $this->majorGridlines;
    }

    public function getMinorGridlines(): ?GridLines
    {
        return $this->minorGridlines;
    }

    public function setMajorGridlines(?GridLines $gridlines): self
    {
        $this->majorGridlines = $gridlines;

        return $this;
    }

    public function setMinorGridlines(?GridLines $gridlines): self
    {
        $this->minorGridlines = $gridlines;

        return $this;
    }

    public function getAxisText(): ?AxisText
    {
        return $this->axisText;
    }

    public function setAxisText(?AxisText $axisText): self
    {
        $this->axisText = $axisText;

        return $this;
    }

    public function setNoFill(bool $noFill): self
    {
        $this->noFill = $noFill;

        return $this;
    }

    public function getNoFill(): bool
    {
        return $this->noFill;
    }
}

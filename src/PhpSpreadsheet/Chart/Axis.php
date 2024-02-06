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
     */
    private ?GridLines $majorGridlines = null;

    /**
     * Chart Minor Gridlines as.
     */
    private ?GridLines $minorGridlines = null;

    /**
     * Axis Number.
     *
     * @var mixed[]
     */
    private array $axisNumber = [
        'format' => self::FORMAT_CODE_GENERAL,
        'source_linked' => 1,
        'numeric' => null,
    ];

    private string $axisType = '';

    private ?AxisText $axisText = null;

    private ?Title $dispUnitsTitle = null;

    /**
     * Axis Options.
     *
     * @var array<string, null|string>
     */
    private array $axisOptions = [
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
        'logBase' => null,
        'dispUnitsBuiltIn' => null,
    ];
    public const DISP_UNITS_HUNDREDS = 'hundreds';
    public const DISP_UNITS_THOUSANDS = 'thousands';
    public const DISP_UNITS_TEN_THOUSANDS = 'tenThousands';
    public const DISP_UNITS_HUNDRED_THOUSANDS = 'hundredThousands';
    public const DISP_UNITS_MILLIONS = 'millions';
    public const DISP_UNITS_TEN_MILLIONS = 'tenMillions';
    public const DISP_UNITS_HUNDRED_MILLIONS = 'hundredMillions';
    public const DISP_UNITS_BILLIONS = 'billions';
    public const DISP_UNITS_TRILLIONS = 'trillions';
    public const TRILLION_INDEX = (PHP_INT_SIZE > 4) ? 1000000000000 : '1000000000000';
    public const DISP_UNITS_BUILTIN_INT = [
        100 => self::DISP_UNITS_HUNDREDS,
        1000 => self::DISP_UNITS_THOUSANDS,
        10000 => self::DISP_UNITS_TEN_THOUSANDS,
        100000 => self::DISP_UNITS_HUNDRED_THOUSANDS,
        1000000 => self::DISP_UNITS_MILLIONS,
        10000000 => self::DISP_UNITS_TEN_MILLIONS,
        100000000 => self::DISP_UNITS_HUNDRED_MILLIONS,
        1000000000 => self::DISP_UNITS_BILLIONS,
        self::TRILLION_INDEX => self::DISP_UNITS_TRILLIONS, // overflow for 32-bit
    ];

    /**
     * Fill Properties.
     */
    private ChartColor $fillColor;

    private const NUMERIC_FORMAT = [
        Properties::FORMAT_CODE_NUMBER,
        Properties::FORMAT_CODE_DATE,
        Properties::FORMAT_CODE_DATE_ISO8601,
    ];

    private bool $noFill = false;

    /**
     * Get Series Data Type.
     */
    public function setAxisNumberProperties(string $format_code, ?bool $numeric = null, int $sourceLinked = 0): void
    {
        $format = $format_code;
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
     */
    public function getAxisNumberFormat(): string
    {
        return $this->axisNumber['format'];
    }

    /**
     * Get Axis Number Source Linked.
     */
    public function getAxisNumberSourceLinked(): string
    {
        return (string) $this->axisNumber['source_linked'];
    }

    public function getAxisIsNumericFormat(): bool
    {
        return $this->axisType === self::AXIS_TYPE_DATE || (bool) $this->axisNumber['numeric'];
    }

    public function setAxisOption(string $key, null|float|int|string $value): void
    {
        if ($value !== null && $value !== '') {
            $this->axisOptions[$key] = (string) $value;
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
        null|float|int|string $minimum = null,
        null|float|int|string $maximum = null,
        null|float|int|string $majorUnit = null,
        null|float|int|string $minorUnit = null,
        null|float|int|string $textRotation = null,
        ?string $hidden = null,
        ?string $baseTimeUnit = null,
        ?string $majorTimeUnit = null,
        ?string $minorTimeUnit = null,
        null|float|int|string $logBase = null,
        ?string $dispUnitsBuiltIn = null
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
        $this->setAxisOption('logBase', $logBase);
        $this->setAxisOption('dispUnitsBuiltIn', $dispUnitsBuiltIn);
    }

    /**
     * Get Axis Options Property.
     */
    public function getAxisOptionsProperty(string $property): ?string
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
     */
    public function setAxisOrientation(string $orientation): void
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
     */
    public function setFillParameters(?string $color, ?int $alpha = null, ?string $AlphaType = ChartColor::EXCEL_COLOR_TYPE_RGB): void
    {
        $this->fillColor->setColorProperties($color, $alpha, $AlphaType);
    }

    /**
     * Get Fill Property.
     */
    public function getFillProperty(string $property): string
    {
        return (string) $this->fillColor->getColorProperty($property);
    }

    public function getFillColorObject(): ChartColor
    {
        return $this->fillColor;
    }

    private string $crossBetween = ''; // 'between' or 'midCat' might be better

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

    public function setDispUnitsTitle(?Title $dispUnitsTitle): self
    {
        $this->dispUnitsTitle = $dispUnitsTitle;

        return $this;
    }

    public function getDispUnitsTitle(): ?Title
    {
        return $this->dispUnitsTitle;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        parent::__clone();
        $this->majorGridlines = ($this->majorGridlines === null) ? null : clone $this->majorGridlines;
        $this->majorGridlines = ($this->minorGridlines === null) ? null : clone $this->minorGridlines;
        $this->axisText = ($this->axisText === null) ? null : clone $this->axisText;
        $this->dispUnitsTitle = ($this->dispUnitsTitle === null) ? null : clone $this->dispUnitsTitle;
        $this->fillColor = clone $this->fillColor;
    }
}

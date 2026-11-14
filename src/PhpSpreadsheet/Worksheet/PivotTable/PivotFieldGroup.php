<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

/**
 * Describes how a single source (cache) field is grouped in a pivot table.
 *
 * Two kinds of grouping are supported:
 *
 * - Numeric range grouping: values are bucketed into fixed-width intervals
 *   between a start and end number (e.g. 0-100, 100-200, ...).
 * - Date grouping: date/time values are grouped by one or more calendar parts
 *   (years, quarters, months, ...).
 *
 * The grouping is emitted into the pivot cache definition; when the workbook is
 * opened, the spreadsheet application applies it while refreshing the pivot.
 *
 * @see PivotTableBuilder
 */
class PivotFieldGroup
{
    const TYPE_NUMERIC = 'numeric';
    const TYPE_DATE = 'date';

    // Date grouping units, matching the rangePr@groupBy attribute values.
    const GROUP_BY_SECONDS = 'seconds';
    const GROUP_BY_MINUTES = 'minutes';
    const GROUP_BY_HOURS = 'hours';
    const GROUP_BY_DAYS = 'days';
    const GROUP_BY_MONTHS = 'months';
    const GROUP_BY_QUARTERS = 'quarters';
    const GROUP_BY_YEARS = 'years';

    private string $type;

    /**
     * Numeric grouping: the width of each bucket.
     */
    private float $interval = 1.0;

    private ?float $startNum = null;

    private ?float $endNum = null;

    /**
     * Date grouping: the calendar units to group by, in coarse-to-fine order.
     *
     * @var string[]
     */
    private array $groupBy = [];

    private ?string $startDate = null;

    private ?string $endDate = null;

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Create a numeric range grouping (buckets of $interval width).
     */
    public static function numeric(float $interval, ?float $startNum = null, ?float $endNum = null): self
    {
        $group = new self(self::TYPE_NUMERIC);
        $group->interval = $interval;
        $group->startNum = $startNum;
        $group->endNum = $endNum;

        return $group;
    }

    /**
     * Create a date grouping by one or more calendar units.
     *
     * @param string|string[] $groupBy one or more GROUP_BY_* constants
     */
    public static function date(array|string $groupBy, ?string $startDate = null, ?string $endDate = null): self
    {
        $group = new self(self::TYPE_DATE);
        $group->groupBy = is_array($groupBy) ? array_values($groupBy) : [$groupBy];
        $group->startDate = $startDate;
        $group->endDate = $endDate;

        return $group;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNumeric(): bool
    {
        return $this->type === self::TYPE_NUMERIC;
    }

    public function isDate(): bool
    {
        return $this->type === self::TYPE_DATE;
    }

    public function getInterval(): float
    {
        return $this->interval;
    }

    public function getStartNum(): ?float
    {
        return $this->startNum;
    }

    public function getEndNum(): ?float
    {
        return $this->endNum;
    }

    /**
     * @return string[]
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * Excel's own ceiling on the number of items in a single pivot field.
     */
    public const MAX_ITEMS = 1048576;

    /**
     * How many items this grouping renders, including the two out-of-range
     * bounds Excel always adds ("<start" and ">end" / "<1/1/1900" and
     * ">12/31/9999").
     *
     * This is the single source of truth for the item count: the pivot field's
     * <items>, the cache definition's <groupItems> and the declared pivot
     * location are all derived from it, so they cannot drift apart.
     */
    public function renderedItemCount(): int
    {
        if ($this->isDate()) {
            return count($this->dateGroupItems());
        }

        return count($this->numericBuckets()) + 2;
    }

    /**
     * The calendar unit this date grouping uses. Excel groups by a single unit
     * per field, so the first requested unit wins; months is the default.
     */
    public function effectiveGroupBy(): string
    {
        return $this->groupBy[0] ?? self::GROUP_BY_MONTHS;
    }

    /**
     * The canonical group-item labels Excel uses for this grouping unit.
     *
     * @return string[]
     */
    public function dateGroupItems(): array
    {
        switch ($this->effectiveGroupBy()) {
            case self::GROUP_BY_QUARTERS:
                return ['<1/1/1900', 'Qtr1', 'Qtr2', 'Qtr3', 'Qtr4', '>12/31/9999'];
            case self::GROUP_BY_MONTHS:
                return [
                    '<1/1/1900', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', '>12/31/9999',
                ];
            //case self::GROUP_BY_YEARS:
            default:
                // Years are enumerated by the application on refresh.
                return ['<1/1/1900', '>12/31/9999'];
        }
    }

    /**
     * The effective numeric bounds and bucket width, with the same fallbacks
     * the writer applies when the caller supplied none.
     *
     * @return array{0: float, 1: float, 2: float}
     */
    public function effectiveNumericRange(): array
    {
        $start = $this->startNum ?? 0.0;
        $end = $this->endNum ?? ($start + $this->interval);
        $interval = $this->interval > 0 ? $this->interval : 1.0;

        return [$start, $end, $interval];
    }

    /**
     * The "<lower>-<upper>" bucket labels for a numeric grouping.
     *
     * The bucket index is computed from the start rather than accumulated, so
     * a fractional interval cannot drift through repeated addition.
     *
     * @return string[]
     */
    public function numericBuckets(): array
    {
        [$start, $end, $interval] = $this->effectiveNumericRange();
        if ($interval <= 0.0 || $end <= $start) {
            return [];
        }

        // The two sentinel bounds count towards the same ceiling as the
        // buckets, so the bucket count is clamped with room for them.
        $count = (int) ceil(($end - $start) / $interval);
        $count = min($count, self::MAX_ITEMS - 2);

        $buckets = [];
        for ($i = 0; $i < $count; ++$i) {
            $lower = $start + ($i * $interval);
            $upper = min($start + (($i + 1) * $interval), $end);
            $buckets[] = self::num($lower) . '-' . self::num($upper);
        }

        return $buckets;
    }

    /**
     * Format a float without a trailing ".0" for whole numbers.
     */
    private static function num(float $value): string
    {
        if ($value === floor($value) && abs($value) < 1.0e15) {
            return (string) (int) $value;
        }

        return (string) $value;
    }
}

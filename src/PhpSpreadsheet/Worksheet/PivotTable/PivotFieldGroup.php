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
}

<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AutoFit
{
    protected Worksheet $worksheet;

    public function __construct(Worksheet $worksheet)
    {
        $this->worksheet = $worksheet;
    }

    public function getAutoFilterIndentRanges(): array
    {
        $autoFilterIndentRanges = [];
        $autoFilterIndentRanges[] = $this->getAutoFilterIndentRange($this->worksheet->getAutoFilter());

        foreach ($this->worksheet->getTableCollection() as $table) {
            /** @var Table $table */
            if ($table->getShowHeaderRow() === true && $table->getAllowFilter() === true) {
                $autoFilter = $table->getAutoFilter();
                $autoFilterIndentRanges[] = $this->getAutoFilterIndentRange($autoFilter);
            }
        }

        return array_filter($autoFilterIndentRanges);
    }

    private function getAutoFilterIndentRange(AutoFilter $autoFilter): ?string
    {
        $autoFilterRange = $autoFilter->getRange();
        $autoFilterIndentRange = null;

        if (!empty($autoFilterRange)) {
            $autoFilterRangeBoundaries = Coordinate::rangeBoundaries($autoFilterRange);
            $autoFilterIndentRange = (string) new CellRange(
                CellAddress::fromColumnAndRow($autoFilterRangeBoundaries[0][0], $autoFilterRangeBoundaries[0][1]),
                CellAddress::fromColumnAndRow($autoFilterRangeBoundaries[1][0], $autoFilterRangeBoundaries[0][1])
            );
        }

        return $autoFilterIndentRange;
    }
}

<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BoxWhiskerSeries
{
    private DataSeriesValues $categories;

    private DataSeriesValues $values;

    private ?DataSeriesValues $label;

    public function __construct(DataSeriesValues $categories, DataSeriesValues $values, ?DataSeriesValues $label = null)
    {
        $this->categories = $categories;
        $this->values = $values;
        $this->label = $label;
    }

    public function getCategories(): DataSeriesValues
    {
        return $this->categories;
    }

    public function getValues(): DataSeriesValues
    {
        return $this->values;
    }

    public function getLabel(): ?DataSeriesValues
    {
        return $this->label;
    }

    public function refresh(Worksheet $worksheet): void
    {
        $this->categories->refresh($worksheet);
        $this->values->refresh($worksheet);

        if ($this->label !== null) {
            $this->label->refresh($worksheet);
        }
    }
}

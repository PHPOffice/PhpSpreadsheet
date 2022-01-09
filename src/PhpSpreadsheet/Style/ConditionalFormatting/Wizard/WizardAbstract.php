<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Style;

abstract class WizardAbstract
{
    /**
     * @var Style $style
     */
    protected $style;

    /**
     * @var string $expression
     */
    protected $expression;

    /**
     * @var string $cellRange
     */
    protected $cellRange;

    /**
     * @var string $referenceCell
     */
    protected $referenceCell;

    public function __construct(string $cellRange)
    {
        $this->cellRange = $cellRange;
        $this->setReferenceCellForExpressions($cellRange);
    }

    protected function setReferenceCellForExpressions(string $conditionalRange)
    {
        $conditionalRange = Coordinate::splitRange(str_replace('$', '', strtoupper($conditionalRange)));
        [$this->referenceCell] = $conditionalRange[0];
    }

    public function getStyle(): Style
    {
        return $this->style ?? new Style(false, true);
    }

    public function setStyle(Style $style)
    {
        $this->style = $style;
    }
}

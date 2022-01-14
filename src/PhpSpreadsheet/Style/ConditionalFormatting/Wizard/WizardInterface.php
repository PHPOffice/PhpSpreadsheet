<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;

interface WizardInterface
{
    public function getConditional(): Conditional;

    public function getStyle(): Style;

    public function setStyle(Style $style): void;

    public function getCellRange(): string;
}
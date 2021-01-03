<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

class ConditionalStyles
{
    private $worksheet;

    private $worksheetXml;

    private $dxfs;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $worksheetXml, array $dxfs = [])
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
        $this->dxfs = $dxfs;
    }

    public function load(): void
    {
        $this->setConditionalStyles(
            $this->worksheet,
            $this->readConditionalStyles($this->worksheetXml)
        );
    }

    private function readConditionalStyles($xmlSheet)
    {
        $conditionals = [];
        foreach ($xmlSheet->conditionalFormatting as $conditional) {
            foreach ($conditional->cfRule as $cfRule) {
                if (((string) $cfRule['type'] == Conditional::CONDITION_NONE
                    || (string) $cfRule['type'] == Conditional::CONDITION_CELLIS
                    || (string) $cfRule['type'] == Conditional::CONDITION_CONTAINSTEXT
                    || (string) $cfRule['type'] == Conditional::CONDITION_CONTAINSBLANKS
                    || (string) $cfRule['type'] == Conditional::CONDITION_NOTCONTAINSBLANKS
                    || (string) $cfRule['type'] == Conditional::CONDITION_EXPRESSION)
                    && isset($this->dxfs[(int) ($cfRule['dxfId'])])) {
                    $conditionals[(string) $conditional['sqref']][(int) ($cfRule['priority'])] = $cfRule;
                }
            }
        }

        return $conditionals;
    }

    private function setConditionalStyles(Worksheet $worksheet, array $conditionals): void
    {
        foreach ($conditionals as $ref => $cfRules) {
            ksort($cfRules);
            $conditionalStyles = $this->readStyleRules($cfRules);

            // Extract all cell references in $ref
            $cellBlocks = explode(' ', str_replace('$', '', strtoupper($ref)));
            foreach ($cellBlocks as $cellBlock) {
                $worksheet->getStyle($cellBlock)->setConditionalStyles($conditionalStyles);
            }
        }
    }

    private function readStyleRules($cfRules)
    {
        $conditionalStyles = [];
        foreach ($cfRules as $cfRule) {
            $objConditional = new Conditional();
            $objConditional->setConditionType((string) $cfRule['type']);
            $objConditional->setOperatorType((string) $cfRule['operator']);

            if ((string) $cfRule['text'] != '') {
                $objConditional->setText((string) $cfRule['text']);
            }

            if (isset($cfRule['stopIfTrue']) && (int) $cfRule['stopIfTrue'] === 1) {
                $objConditional->setStopIfTrue(true);
            }

            if (count($cfRule->formula) > 1) {
                foreach ($cfRule->formula as $formula) {
                    $objConditional->addCondition((string) $formula);
                }
            } else {
                $objConditional->addCondition((string) $cfRule->formula);
            }
            $objConditional->setStyle(clone $this->dxfs[(int) ($cfRule['dxfId'])]);
            $conditionalStyles[] = $objConditional;
        }

        return $conditionalStyles;
    }
}

<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBar;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormattingRuleExtension;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
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
            $this->readConditionalStyles($this->worksheetXml),
            $this->worksheetXml->extLst
        );
    }

    private function readConditionalStyles($xmlSheet)
    {
        $conditionals = [];
        foreach ($xmlSheet->conditionalFormatting as $conditional) {
            foreach ($conditional->cfRule as $cfRule) {
                if (Conditional::isValidConditionType((string) $cfRule['type']) && isset($this->dxfs[(int) ($cfRule['dxfId'])])) {
                    $conditionals[(string) $conditional['sqref']][(int) ($cfRule['priority'])] = $cfRule;
                } elseif ((string) $cfRule['type'] == Conditional::CONDITION_DATABAR) {
                    $conditionals[(string) $conditional['sqref']][(int) ($cfRule['priority'])] = $cfRule;
                }
            }
        }

        return $conditionals;
    }

    private function setConditionalStyles(Worksheet $worksheet, array $conditionals, $xmlExtLst): void
    {
        foreach ($conditionals as $ref => $cfRules) {
            ksort($cfRules);
            $conditionalStyles = $this->readStyleRules($cfRules, $xmlExtLst);

            // Extract all cell references in $ref
            $cellBlocks = explode(' ', str_replace('$', '', strtoupper($ref)));
            foreach ($cellBlocks as $cellBlock) {
                $worksheet->getStyle($cellBlock)->setConditionalStyles($conditionalStyles);
            }
        }
    }

    private function readStyleRules($cfRules, $extLst)
    {
        $conditionalFormattingRuleExtensions = ConditionalFormattingRuleExtension::parseExtLstXml($extLst);
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

            if (isset($cfRule->dataBar)) {
                $objConditional->setDataBar(
                    $this->readDataBarOfConditionalRule($cfRule, $conditionalFormattingRuleExtensions)
                );
            } else {
                $objConditional->setStyle(clone $this->dxfs[(int) ($cfRule['dxfId'])]);
            }

            $conditionalStyles[] = $objConditional;
        }

        return $conditionalStyles;
    }

    private function readDataBarOfConditionalRule($cfRule, $conditionalFormattingRuleExtensions): ConditionalDataBar
    {
        $dataBar = new ConditionalDataBar();
        //dataBar attribute
        if (isset($cfRule->dataBar['showValue'])) {
            $dataBar->setShowValue((bool) $cfRule->dataBar['showValue']);
        }

        //dataBar children
        //conditionalFormatValueObjects
        $cfvoXml = $cfRule->dataBar->cfvo;
        $cfvoIndex = 0;
        foreach ((count($cfvoXml) > 1 ? $cfvoXml : [$cfvoXml]) as $cfvo) {
            if ($cfvoIndex === 0) {
                $dataBar->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject((string) $cfvo['type'], (string) $cfvo['val']));
            }
            if ($cfvoIndex === 1) {
                $dataBar->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject((string) $cfvo['type'], (string) $cfvo['val']));
            }
            ++$cfvoIndex;
        }

        //color
        if (isset($cfRule->dataBar->color)) {
            $dataBar->setColor((string) $cfRule->dataBar->color['rgb']);
        }
        //extLst
        $this->readDataBarExtLstOfConditionalRule($dataBar, $cfRule, $conditionalFormattingRuleExtensions);

        return $dataBar;
    }

    private function readDataBarExtLstOfConditionalRule(ConditionalDataBar $dataBar, $cfRule, $conditionalFormattingRuleExtensions): void
    {
        if (isset($cfRule->extLst)) {
            $ns = $cfRule->extLst->getNamespaces(true);
            foreach ((count($cfRule->extLst) > 0 ? $cfRule->extLst->ext : [$cfRule->extLst->ext]) as $ext) {
                $extId = (string) $ext->children($ns['x14'])->id;
                if (isset($conditionalFormattingRuleExtensions[$extId]) && (string) $ext['uri'] === '{B025F937-C7B1-47D3-B67F-A62EFF666E3E}') {
                    $dataBar->setConditionalFormattingRuleExt($conditionalFormattingRuleExtensions[$extId]);
                }
            }
        }
    }
}

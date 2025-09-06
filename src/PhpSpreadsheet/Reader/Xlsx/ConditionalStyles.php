<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Styles as StyleReader;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalColorScale;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBar;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormattingRuleExtension;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalIconSet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\IconSetValues;
use PhpOffice\PhpSpreadsheet\Style\Style as Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;
use stdClass;

class ConditionalStyles
{
    private Worksheet $worksheet;

    private SimpleXMLElement $worksheetXml;

    /** @var string[] */
    private array $ns;

    /** @var Style[] */
    private array $dxfs;

    private StyleReader $styleReader;

    /** @param Style[] $dxfs */
    public function __construct(Worksheet $workSheet, SimpleXMLElement $worksheetXml, array $dxfs, StyleReader $styleReader)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
        $this->dxfs = $dxfs;
        $this->styleReader = $styleReader;
    }

    public function load(): void
    {
        $selectedCells = $this->worksheet->getSelectedCells();

        $this->setConditionalStyles(
            $this->worksheet,
            $this->readConditionalStyles($this->worksheetXml),
            $this->worksheetXml->extLst
        );

        $this->worksheet->setSelectedCells($selectedCells);
    }

    public function loadFromExt(): void
    {
        $selectedCells = $this->worksheet->getSelectedCells();

        $this->ns = $this->worksheetXml->getNamespaces(true);
        $this->setConditionalsFromExt(
            $this->readConditionalsFromExt($this->worksheetXml->extLst)
        );

        $this->worksheet->setSelectedCells($selectedCells);
    }

    /** @param Conditional[][] $conditionals */
    private function setConditionalsFromExt(array $conditionals): void
    {
        foreach ($conditionals as $conditionalRange => $cfRules) {
            ksort($cfRules);
            // Priority is used as the key for sorting; but may not start at 0,
            // so we use array_values to reset the index after sorting.
            $existing = $this->worksheet->getConditionalStylesCollection();
            if (array_key_exists($conditionalRange, $existing)) {
                $conditionalStyle = $existing[$conditionalRange];
                $cfRules = array_merge($conditionalStyle, $cfRules);
            }
            $this->worksheet->getStyle($conditionalRange)
                ->setConditionalStyles(array_values($cfRules));
        }
    }

    /** @return array<string, array<int, Conditional>> */
    private function readConditionalsFromExt(SimpleXMLElement $extLst): array
    {
        $conditionals = [];
        if (!isset($extLst->ext)) {
            return $conditionals;
        }

        foreach ($extLst->ext as $extlstcond) {
            $extAttrs = $extlstcond->attributes() ?? [];
            $extUri = (string) ($extAttrs['uri'] ?? '');
            if ($extUri !== '{78C0D931-6437-407d-A8EE-F0AAD7539E65}') {
                continue;
            }
            $conditionalFormattingRuleXml = $extlstcond->children($this->ns['x14']);
            if (!$conditionalFormattingRuleXml->conditionalFormattings) {
                return [];
            }

            foreach ($conditionalFormattingRuleXml->children($this->ns['x14']) as $extFormattingXml) {
                $extFormattingRangeXml = $extFormattingXml->children($this->ns['xm']);
                if (!$extFormattingRangeXml->sqref) {
                    continue;
                }

                $sqref = (string) $extFormattingRangeXml->sqref;
                $extCfRuleXml = $extFormattingXml->cfRule;

                $attributes = $extCfRuleXml->attributes();
                if (!$attributes) {
                    continue;
                }
                $conditionType = (string) $attributes->type;
                if (
                    !Conditional::isValidConditionType($conditionType)
                    || $conditionType === Conditional::CONDITION_DATABAR
                ) {
                    continue;
                }

                $priority = (int) $attributes->priority;

                $conditional = $this->readConditionalRuleFromExt($extCfRuleXml, $attributes);
                $cfStyle = $this->readStyleFromExt($extCfRuleXml);
                $conditional->setStyle($cfStyle);
                $conditionals[$sqref][$priority] = $conditional;
            }
        }

        return $conditionals;
    }

    private function readConditionalRuleFromExt(SimpleXMLElement $cfRuleXml, SimpleXMLElement $attributes): Conditional
    {
        $conditionType = (string) $attributes->type;
        $operatorType = (string) $attributes->operator;
        $priority = (int) (string) $attributes->priority;
        $stopIfTrue = (int) (string) $attributes->stopIfTrue;

        $operands = [];
        foreach ($cfRuleXml->children($this->ns['xm']) as $cfRuleOperandsXml) {
            $operands[] = (string) $cfRuleOperandsXml;
        }

        $conditional = new Conditional();
        $conditional->setConditionType($conditionType);
        $conditional->setOperatorType($operatorType);
        $conditional->setPriority($priority);
        $conditional->setStopIfTrue($stopIfTrue === 1);
        if (
            $conditionType === Conditional::CONDITION_CONTAINSTEXT
            || $conditionType === Conditional::CONDITION_NOTCONTAINSTEXT
            || $conditionType === Conditional::CONDITION_BEGINSWITH
            || $conditionType === Conditional::CONDITION_ENDSWITH
            || $conditionType === Conditional::CONDITION_TIMEPERIOD
        ) {
            $conditional->setText(array_pop($operands) ?? '');
        }
        $conditional->setConditions($operands);

        return $conditional;
    }

    private function readStyleFromExt(SimpleXMLElement $extCfRuleXml): Style
    {
        $cfStyle = new Style(false, true);
        if ($extCfRuleXml->dxf) {
            $styleXML = $extCfRuleXml->dxf->children();

            if ($styleXML->borders) {
                $this->styleReader->readBorderStyle($cfStyle->getBorders(), $styleXML->borders);
            }
            if ($styleXML->fill) {
                $this->styleReader->readFillStyle($cfStyle->getFill(), $styleXML->fill);
            }
            if ($styleXML->font) {
                $this->styleReader->readFontStyle($cfStyle->getFont(), $styleXML->font);
            }
        }

        return $cfStyle;
    }

    /** @return mixed[] */
    private function readConditionalStyles(SimpleXMLElement $xmlSheet): array
    {
        $conditionals = [];
        foreach ($xmlSheet->conditionalFormatting as $conditional) {
            foreach ($conditional->cfRule as $cfRule) {
                if (Conditional::isValidConditionType((string) $cfRule['type']) && (!isset($cfRule['dxfId']) || isset($this->dxfs[(int) ($cfRule['dxfId'])]))) {
                    $conditionals[(string) $conditional['sqref']][(int) ($cfRule['priority'])] = $cfRule;
                } elseif ((string) $cfRule['type'] == Conditional::CONDITION_DATABAR) {
                    $conditionals[(string) $conditional['sqref']][(int) ($cfRule['priority'])] = $cfRule;
                }
            }
        }

        return $conditionals;
    }

    /** @param mixed[] $conditionals */
    private function setConditionalStyles(Worksheet $worksheet, array $conditionals, SimpleXMLElement $xmlExtLst): void
    {
        foreach ($conditionals as $cellRangeReference => $cfRules) {
            /** @var mixed[] $cfRules */
            ksort($cfRules); // no longer needed for Xlsx, but helps Xls
            $conditionalStyles = $this->readStyleRules($cfRules, $xmlExtLst);

            // Extract all cell references in $cellRangeReference
            // N.B. In Excel UI, intersection is space and union is comma.
            // But in Xml, intersection is comma and union is space.
            $cellRangeReference = str_replace(['$', ' ', ',', '^'], ['', '^', ' ', ','], strtoupper($cellRangeReference));

            foreach ($conditionalStyles as $cs) {
                $scale = $cs->getColorScale();
                if ($scale !== null) {
                    $scale->setSqRef($cellRangeReference, $worksheet);
                }
            }
            $worksheet->getStyle($cellRangeReference)->setConditionalStyles($conditionalStyles);
        }
    }

    /**
     * @param mixed[] $cfRules
     *
     * @return Conditional[]
     */
    private function readStyleRules(array $cfRules, SimpleXMLElement $extLst): array
    {
        /** @var ConditionalFormattingRuleExtension[] */
        $conditionalFormattingRuleExtensions = ConditionalFormattingRuleExtension::parseExtLstXml($extLst);
        $conditionalStyles = [];

        /** @var SimpleXMLElement $cfRule */
        foreach ($cfRules as $cfRule) {
            $objConditional = new Conditional();
            $objConditional->setConditionType((string) $cfRule['type']);
            $objConditional->setOperatorType((string) $cfRule['operator']);
            $objConditional->setPriority((int) (string) $cfRule['priority']);
            $objConditional->setNoFormatSet(!isset($cfRule['dxfId']));

            if ((string) $cfRule['text'] != '') {
                $objConditional->setText((string) $cfRule['text']);
            } elseif ((string) $cfRule['timePeriod'] != '') {
                $objConditional->setText((string) $cfRule['timePeriod']);
            }

            if (isset($cfRule['stopIfTrue']) && (int) $cfRule['stopIfTrue'] === 1) {
                $objConditional->setStopIfTrue(true);
            }

            if (count($cfRule->formula) >= 1) {
                foreach ($cfRule->formula as $formulax) {
                    $formula = (string) $formulax;
                    $formula = str_replace(['_xlfn.', '_xlws.'], '', $formula);
                    if ($formula === 'TRUE') {
                        $objConditional->addCondition(true);
                    } elseif ($formula === 'FALSE') {
                        $objConditional->addCondition(false);
                    } else {
                        $objConditional->addCondition($formula);
                    }
                }
            } else {
                $objConditional->addCondition('');
            }

            if (isset($cfRule->dataBar)) {
                $objConditional->setDataBar(
                    $this->readDataBarOfConditionalRule($cfRule, $conditionalFormattingRuleExtensions)
                );
            } elseif (isset($cfRule->colorScale)) {
                $objConditional->setColorScale(
                    $this->readColorScale($cfRule)
                );
            } elseif (isset($cfRule->iconSet)) {
                $objConditional->setIconSet($this->readIconSet($cfRule));
            } elseif (isset($cfRule['dxfId'])) {
                $objConditional->setStyle(clone $this->dxfs[(int) ($cfRule['dxfId'])]);
            }

            $conditionalStyles[] = $objConditional;
        }

        return $conditionalStyles;
    }

    /** @param ConditionalFormattingRuleExtension[] $conditionalFormattingRuleExtensions */
    private function readDataBarOfConditionalRule(SimpleXMLElement $cfRule, array $conditionalFormattingRuleExtensions): ConditionalDataBar
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
        foreach ((count($cfvoXml) > 1 ? $cfvoXml : [$cfvoXml]) as $cfvo) { //* @phpstan-ignore-line
            /** @var SimpleXMLElement $cfvo */
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
            $dataBar->setColor($this->styleReader->readColor($cfRule->dataBar->color));
        }
        //extLst
        $this->readDataBarExtLstOfConditionalRule($dataBar, $cfRule, $conditionalFormattingRuleExtensions);

        return $dataBar;
    }

    private function readColorScale(SimpleXMLElement|stdClass $cfRule): ConditionalColorScale
    {
        $colorScale = new ConditionalColorScale();
        /** @var SimpleXMLElement $cfRule */
        $count = count($cfRule->colorScale->cfvo);
        $idx = 0;
        foreach ($cfRule->colorScale->cfvo as $cfvoXml) {
            $attr = $cfvoXml->attributes() ?? [];
            $type = (string) ($attr['type'] ?? '');
            $val = $attr['val'] ?? null;
            if ($idx === 0) {
                $method = 'setMinimumConditionalFormatValueObject';
            } elseif ($idx === 1 && $count === 3) {
                $method = 'setMidpointConditionalFormatValueObject';
            } else {
                $method = 'setMaximumConditionalFormatValueObject';
            }
            if ($type !== 'formula') {
                $colorScale->$method(new ConditionalFormatValueObject($type, $val));
            } else {
                $colorScale->$method(new ConditionalFormatValueObject($type, null, $val));
            }
            ++$idx;
        }
        $idx = 0;
        foreach ($cfRule->colorScale->color as $color) {
            $rgb = $this->styleReader->readColor($color);
            if ($idx === 0) {
                $colorScale->setMinimumColor(new Color($rgb));
            } elseif ($idx === 1 && $count === 3) {
                $colorScale->setMidpointColor(new Color($rgb));
            } else {
                $colorScale->setMaximumColor(new Color($rgb));
            }
            ++$idx;
        }

        return $colorScale;
    }

    private function readIconSet(SimpleXMLElement $cfRule): ConditionalIconSet
    {
        $iconSet = new ConditionalIconSet();

        if (isset($cfRule->iconSet['iconSet'])) {
            $iconSet->setIconSetType(IconSetValues::from($cfRule->iconSet['iconSet']));
        }
        if (isset($cfRule->iconSet['reverse'])) {
            $iconSet->setReverse('1' === (string) $cfRule->iconSet['reverse']);
        }
        if (isset($cfRule->iconSet['showValue'])) {
            $iconSet->setShowValue('1' === (string) $cfRule->iconSet['showValue']);
        }
        if (isset($cfRule->iconSet['custom'])) {
            $iconSet->setCustom('1' === (string) $cfRule->iconSet['custom']);
        }

        $cfvos = [];
        foreach ($cfRule->iconSet->cfvo as $cfvoXml) {
            $type = (string) $cfvoXml['type'];
            $value = (string) ($cfvoXml['val'] ?? '');
            $cfvo = new ConditionalFormatValueObject($type, $value);
            if (isset($cfvoXml['gte'])) {
                $cfvo->setGreaterThanOrEqual('1' === (string) $cfvoXml['gte']);
            }
            $cfvos[] = $cfvo;
        }
        $iconSet->setCfvos($cfvos);

        // TODO: The cfIcon element is not implemented yet.

        return $iconSet;
    }

    /** @param ConditionalFormattingRuleExtension[] $conditionalFormattingRuleExtensions */
    private function readDataBarExtLstOfConditionalRule(ConditionalDataBar $dataBar, SimpleXMLElement $cfRule, array $conditionalFormattingRuleExtensions): void
    {
        if (isset($cfRule->extLst)) {
            $ns = $cfRule->extLst->getNamespaces(true);
            foreach ((count($cfRule->extLst) > 0 ? $cfRule->extLst->ext : [$cfRule->extLst->ext]) as $ext) { //* @phpstan-ignore-line
                /** @var SimpleXMLElement $ext */
                $extId = (string) $ext->children($ns['x14'])->id;
                if (isset($conditionalFormattingRuleExtensions[$extId]) && (string) $ext['uri'] === '{B025F937-C7B1-47D3-B67F-A62EFF666E3E}') {
                    $dataBar->setConditionalFormattingRuleExt($conditionalFormattingRuleExtensions[$extId]);
                }
            }
        }
    }
}

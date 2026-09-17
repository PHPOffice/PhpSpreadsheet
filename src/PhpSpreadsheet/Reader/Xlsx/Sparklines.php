<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\Sparkline;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SimpleXMLElement;

/**
 * Reads the `x14:sparklineGroups` extension from a worksheet's `extLst` and
 * populates the worksheet's sparkline group collection.
 */
class Sparklines extends BaseParserClass
{
    private Worksheet $worksheet;

    private SimpleXMLElement $worksheetXml;

    public function __construct(Worksheet $workSheet, SimpleXMLElement $worksheetXml)
    {
        $this->worksheet = $workSheet;
        $this->worksheetXml = $worksheetXml;
    }

    public function load(): void
    {
        if (!isset($this->worksheetXml->extLst->ext)) {
            return;
        }

        foreach ($this->worksheetXml->extLst->ext as $ext) {
            $extAttrs = $ext->attributes() ?? [];
            if ((string) ($extAttrs['uri'] ?? '') !== Namespaces::SPARKLINE_URI) {
                continue;
            }

            $groups = $ext->children(Namespaces::DATA_VALIDATIONS1)->sparklineGroups;
            if (!isset($groups)) {
                continue;
            }

            $groupList = $groups->children(Namespaces::DATA_VALIDATIONS1)->sparklineGroup ?? null;
            if ($groupList === null) {
                continue;
            }

            foreach ($groupList as $groupXml) {
                $group = $this->readGroup($groupXml);
                if ($group !== null) {
                    $this->worksheet->addSparklineGroup($group);
                }
            }
        }
    }

    private function readGroup(SimpleXMLElement $groupXml): ?SparklineGroup
    {
        $group = new SparklineGroup();
        $attributes = $groupXml->attributes() ?? [];

        $typeValue = isset($attributes['type']) ? (string) $attributes['type'] : SparklineType::Line->value;
        $group->setType(SparklineType::tryFrom($typeValue) ?? SparklineType::Line);

        if (isset($attributes['lineWeight'])) {
            $group->setLineWeight((float) $attributes['lineWeight']);
        }
        if (isset($attributes['displayEmptyCellsAs'])) {
            $group->setDisplayEmptyCellsAs((string) $attributes['displayEmptyCellsAs']);
        }
        if (isset($attributes['minAxisType'])) {
            $group->setMinAxisType((string) $attributes['minAxisType']);
        }
        if (isset($attributes['maxAxisType'])) {
            $group->setMaxAxisType((string) $attributes['maxAxisType']);
        }
        if (isset($attributes['manualMin'])) {
            $group->setManualMin((float) $attributes['manualMin']);
        }
        if (isset($attributes['manualMax'])) {
            $group->setManualMax((float) $attributes['manualMax']);
        }

        $group->setDisplayMarkers(self::boolAttr($attributes, 'markers'));
        $group->setDisplayHigh(self::boolAttr($attributes, 'high'));
        $group->setDisplayLow(self::boolAttr($attributes, 'low'));
        $group->setDisplayFirst(self::boolAttr($attributes, 'first'));
        $group->setDisplayLast(self::boolAttr($attributes, 'last'));
        $group->setDisplayNegative(self::boolAttr($attributes, 'negative'));
        $group->setDisplayXAxis(self::boolAttr($attributes, 'displayXAxis'));
        $group->setDisplayHidden(self::boolAttr($attributes, 'displayHidden'));
        $group->setRightToLeft(self::boolAttr($attributes, 'rightToLeft'));

        $this->readColors($group, $groupXml);
        $this->readSparklines($group, $groupXml);

        // A group with no sparklines carries no useful information.
        return $group->getSparklines() === [] ? null : $group;
    }

    private function readColors(SparklineGroup $group, SimpleXMLElement $groupXml): void
    {
        $children = $groupXml->children(Namespaces::DATA_VALIDATIONS1);
        $group->setColorSeries(self::color($children->colorSeries));
        $group->setColorNegative(self::color($children->colorNegative));
        $group->setColorAxis(self::color($children->colorAxis));
        $group->setColorMarkers(self::color($children->colorMarkers));
        $group->setColorFirst(self::color($children->colorFirst));
        $group->setColorLast(self::color($children->colorLast));
        $group->setColorHigh(self::color($children->colorHigh));
        $group->setColorLow(self::color($children->colorLow));
    }

    private function readSparklines(SparklineGroup $group, SimpleXMLElement $groupXml): void
    {
        $sparklinesXml = $groupXml->children(Namespaces::DATA_VALIDATIONS1)->sparklines;
        if (!isset($sparklinesXml)) {
            return;
        }

        $sparklineList = $sparklinesXml->children(Namespaces::DATA_VALIDATIONS1)->sparkline ?? null;
        if ($sparklineList === null) {
            return;
        }

        foreach ($sparklineList as $sparklineXml) {
            $xm = $sparklineXml->children(Namespaces::DATA_VALIDATIONS2);
            $dataRange = isset($xm->f) ? (string) $xm->f : '';
            $location = isset($xm->sqref) ? (string) $xm->sqref : '';
            if ($location === '') {
                continue;
            }
            $group->addSparkline(new Sparkline($location, $dataRange));
        }
    }

    /**
     * @param iterable<SimpleXMLElement>|SimpleXMLElement $attributes
     */
    private static function boolAttr(mixed $attributes, string $name): bool
    {
        if (!($attributes instanceof SimpleXMLElement)) {
            return false;
        }
        $attrs = $attributes;

        return isset($attrs[$name]) && self::boolean((string) $attrs[$name]);
    }

    /**
     * Extract an ARGB colour from an `x14:color*` element's `rgb` attribute.
     */
    private static function color(?SimpleXMLElement $colorXml): ?string
    {
        if ($colorXml === null || !isset($colorXml[0])) {
            return null;
        }
        $attrs = $colorXml->attributes() ?? [];

        return isset($attrs['rgb']) ? (string) $attrs['rgb'] : null;
    }
}

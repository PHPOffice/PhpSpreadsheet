<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use SimpleXMLElement;

class ConditionalFormattingRuleExtension
{
    const CONDITION_EXTENSION_DATABAR = 'dataBar';

    /** <conditionalFormatting> attributes */
    private $id;

    /** @var string Conditional Formatting Rule */
    private $cfRule;

    /** <conditionalFormatting> children */

    /** @var ConditionalDataBarExtension */
    private $dataBar;

    /** @var string Sequence of References */
    private $sqref;

    /**
     * ConditionalFormattingRuleExtension constructor.
     */
    public function __construct($id = null, string $cfRule = self::CONDITION_EXTENSION_DATABAR)
    {
        if (null === $id) {
            $this->id = '{' . $this->generateUuid() . '}';
        } else {
            $this->id = $id;
        }
        $this->cfRule = $cfRule;
    }

    private function generateUuid()
    {
        $chars = str_split('xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx');

        foreach ($chars as $i => $char) {
            if ($char === 'x') {
                $chars[$i] = dechex(random_int(0, 15));
            } elseif ($char === 'y') {
                $chars[$i] = dechex(random_int(8, 11));
            }
        }

        return implode('', $chars);
    }

    public static function parseExtLstXml($extLstXml)
    {
        $conditionalFormattingRuleExtensions = [];
        $conditionalFormattingRuleExtensionXml = null;
        if ($extLstXml instanceof SimpleXMLElement) {
            foreach ((count($extLstXml) > 0 ? $extLstXml : [$extLstXml]) as $extLst) {
                //this uri is conditionalFormattings
                //https://docs.microsoft.com/en-us/openspecs/office_standards/ms-xlsx/07d607af-5618-4ca2-b683-6a78dc0d9627
                if (isset($extLst->ext['uri']) && (string) $extLst->ext['uri'] === '{78C0D931-6437-407d-A8EE-F0AAD7539E65}') {
                    $conditionalFormattingRuleExtensionXml = $extLst->ext;
                }
            }

            if ($conditionalFormattingRuleExtensionXml) {
                $ns = $conditionalFormattingRuleExtensionXml->getNamespaces(true);
                $extFormattingsXml = $conditionalFormattingRuleExtensionXml->children($ns['x14']);

                foreach ($extFormattingsXml->children($ns['x14']) as $extFormattingXml) {
                    $extCfRuleXml = $extFormattingXml->cfRule;
                    if (((string) $extCfRuleXml->attributes()->type) !== Conditional::CONDITION_DATABAR) {
                        continue;
                    }

                    $extFormattingRuleObj = new self((string) $extCfRuleXml->attributes()->id);
                    $extFormattingRuleObj->setSqref((string) $extFormattingXml->children($ns['xm'])->sqref);
                    $conditionalFormattingRuleExtensions[$extFormattingRuleObj->getId()] = $extFormattingRuleObj;

                    $extDataBarObj = new ConditionalDataBarExtension();
                    $extFormattingRuleObj->setDataBarExt($extDataBarObj);
                    $dataBarXml = $extCfRuleXml->dataBar;
                    self::parseExtDataBarAttributesFromXml($extDataBarObj, $dataBarXml);
                    self::parseExtDataBarElementChildrenFromXml($extDataBarObj, $dataBarXml, $ns);
                }
            }
        }

        return $conditionalFormattingRuleExtensions;
    }

    private static function parseExtDataBarAttributesFromXml(
        ConditionalDataBarExtension $extDataBarObj,
        SimpleXMLElement $dataBarXml
    ): void {
        $dataBarAttribute = $dataBarXml->attributes();
        if ($dataBarAttribute->minLength) {
            $extDataBarObj->setMinLength((int) $dataBarAttribute->minLength);
        }
        if ($dataBarAttribute->maxLength) {
            $extDataBarObj->setMaxLength((int) $dataBarAttribute->maxLength);
        }
        if ($dataBarAttribute->border) {
            $extDataBarObj->setBorder((bool) (string) $dataBarAttribute->border);
        }
        if ($dataBarAttribute->gradient) {
            $extDataBarObj->setGradient((bool) (string) $dataBarAttribute->gradient);
        }
        if ($dataBarAttribute->direction) {
            $extDataBarObj->setDirection((string) $dataBarAttribute->direction);
        }
        if ($dataBarAttribute->negativeBarBorderColorSameAsPositive) {
            $extDataBarObj->setNegativeBarBorderColorSameAsPositive((bool) (string) $dataBarAttribute->negativeBarBorderColorSameAsPositive);
        }
        if ($dataBarAttribute->axisPosition) {
            $extDataBarObj->setAxisPosition((string) $dataBarAttribute->axisPosition);
        }
    }

    private static function parseExtDataBarElementChildrenFromXml(ConditionalDataBarExtension $extDataBarObj, SimpleXMLElement $dataBarXml, $ns): void
    {
        if ($dataBarXml->borderColor) {
            $extDataBarObj->setBorderColor((string) $dataBarXml->borderColor->attributes()['rgb']);
        }
        if ($dataBarXml->negativeFillColor) {
            $extDataBarObj->setNegativeFillColor((string) $dataBarXml->negativeFillColor->attributes()['rgb']);
        }
        if ($dataBarXml->negativeBorderColor) {
            $extDataBarObj->setNegativeBorderColor((string) $dataBarXml->negativeBorderColor->attributes()['rgb']);
        }
        if ($dataBarXml->axisColor) {
            $axisColorAttr = $dataBarXml->axisColor->attributes();
            $extDataBarObj->setAxisColor((string) $axisColorAttr['rgb'], (string) $axisColorAttr['theme'], (string) $axisColorAttr['tint']);
        }
        $cfvoIndex = 0;
        foreach ($dataBarXml->cfvo as $cfvo) {
            $f = (string) $cfvo->children($ns['xm'])->f;
            if ($cfvoIndex === 0) {
                $extDataBarObj->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject((string) $cfvo->attributes()['type'], null, (empty($f) ? null : $f)));
            }
            if ($cfvoIndex === 1) {
                $extDataBarObj->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject((string) $cfvo->attributes()['type'], null, (empty($f) ? null : $f)));
            }
            ++$cfvoIndex;
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCfRule(): string
    {
        return $this->cfRule;
    }

    public function setCfRule(string $cfRule): self
    {
        $this->cfRule = $cfRule;

        return $this;
    }

    public function getDataBarExt(): ConditionalDataBarExtension
    {
        return $this->dataBar;
    }

    public function setDataBarExt(ConditionalDataBarExtension $dataBar): self
    {
        $this->dataBar = $dataBar;

        return $this;
    }

    public function getSqref(): string
    {
        return $this->sqref;
    }

    public function setSqref(string $sqref): self
    {
        $this->sqref = $sqref;

        return $this;
    }
}

<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

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
     *
     * @param $id
     */
    public function __construct($id, string $cfRule = self::CONDITION_EXTENSION_DATABAR)
    {
        $this->id = $id;
        $this->cfRule = $cfRule;
    }

    public static function parseExtLstXml($extLstXml)
    {
        $conditionalFormattingRuleExtensions = [];
        $conditionalFormattingRuleExtensionXml = null;
        if ($extLstXml instanceof SimpleXMLElement) {
            foreach ((count($extLstXml) > 0 ? $extLstXml : [$extLstXml]) as $extLst) {
                //this uri is conditionalFormattings
                //https://docs.microsoft.com/en-us/openspecs/office_standards/ms-xlsx/07d607af-5618-4ca2-b683-6a78dc0d9627
                if ((string) $extLst->ext['uri'] === '{78C0D931-6437-407d-A8EE-F0AAD7539E65}') {
                    $conditionalFormattingRuleExtensionXml = $extLst->ext;
                }
            }
            if ($conditionalFormattingRuleExtensionXml) {
                $ns = $conditionalFormattingRuleExtensionXml->getNamespaces(true);
                $extFormattingsXml = $conditionalFormattingRuleExtensionXml->children($ns['x14']);

                foreach ($extFormattingsXml->children($ns['x14']) as $extFormattingXml) {
                    $extCfRuleXml = $extFormattingXml->cfRule;
                    $extFormattingRuleObj = new self((string) $extCfRuleXml->attributes()->id);
                    $extFormattingRuleObj->setSqref((string) $extFormattingXml->children($ns['xm'])->sqref);
                    $conditionalFormattingRuleExtensions[$extFormattingRuleObj->getId()] = $extFormattingRuleObj;

                    $extDataBarObj = new ConditionalDataBarExtension();
                    $extFormattingRuleObj->setDataBar($extDataBarObj);

                    $dataBarXml = $extCfRuleXml->dataBar;
                    $dataBarAttribute = $dataBarXml->attributes();

                    //attributes
                    if ($dataBarAttribute->minLength) {
                        $extDataBarObj->setMinLength((int) $dataBarAttribute->minLength);
                    }
                    if ($dataBarAttribute->maxLength) {
                        $extDataBarObj->setMaxLength((int) $dataBarAttribute->maxLength);
                    }
                    if ($dataBarAttribute->border) {
                        $extDataBarObj->setBorder((int) $dataBarAttribute->border);
                    }
                    if ($dataBarAttribute->gradient) {
                        $extDataBarObj->setGradient((int) $dataBarAttribute->gradient);
                    }
                    if ($dataBarAttribute->direction) {
                        $extDataBarObj->setDirection((string) $dataBarAttribute->direction);
                    }
                    if ($dataBarAttribute->negativeBarBorderColorSameAsPositive) {
                        $extDataBarObj->setNegativeBarBorderColorSameAsPositive((int) $dataBarAttribute->negativeBarBorderColorSameAsPositive);
                    }
                    if ($dataBarAttribute->axisPosition) {
                        $extDataBarObj->setAxisPosition((string) $dataBarAttribute->axisPosition);
                    }

                    //children
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
                    foreach ($dataBarXml->cfvo as $cfvo) {
                        $f = (string) $cfvo->children($ns['xm'])->f;
                        $extDataBarObj->addConditionalFormatValueObject((string) $cfvo->attributes()['type'], null, (empty($f) ? null : $f));
                    }
                }
            }
        }

        return $conditionalFormattingRuleExtensions;
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

    public function getDataBar(): ConditionalDataBarExtension
    {
        return $this->dataBar;
    }

    public function setDataBar(ConditionalDataBarExtension $dataBar): self
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

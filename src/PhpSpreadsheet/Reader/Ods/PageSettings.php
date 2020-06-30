<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PageSettings
{
    private $officeNs;
    private $stylesNs;

    private $pageLayoutStyles = [];

    private $masterStylesCrossReference = [];

    private $masterPrintStylesCrossReference = [];

    public function __construct(DOMDocument $styleDom)
    {
        $this->setDomNameSpaces($styleDom);
        $this->readPageSettingStyles($styleDom);
        $this->readStyleMasterLookup($styleDom);
    }

    private function setDomNameSpaces(DOMDocument $styleDom): void
    {
        $this->officeNs = $styleDom->lookupNamespaceUri('office');
        $this->stylesNs = $styleDom->lookupNamespaceUri('style');
    }

    private function readPageSettingStyles(DOMDocument $styleDom): void
    {
        $styles = $styleDom->getElementsByTagNameNS($this->officeNs, 'automatic-styles')
            ->item(0)
            ->getElementsByTagNameNS($this->stylesNs, 'page-layout');

        foreach ($styles as $styleSet) {
            $styleName = $styleSet->getAttributeNS($this->stylesNs, 'name');
            $pageLayoutProperties = $styleSet->getElementsByTagNameNS($this->stylesNs, 'page-layout-properties')[0];
            $styleOrientation = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'print-orientation');
            $styleScale = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'scale-to');
            $stylePrintOrder = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'print-page-order');

            $this->pageLayoutStyles[$styleName] = (object) [
                'orientation' => $styleOrientation,
                'scale' => $styleScale,
                'printOrder' => $stylePrintOrder,
            ];
        }
    }

    private function readStyleMasterLookup(DOMDocument $styleDom): void
    {
        $styleMasterLookup = $styleDom->getElementsByTagNameNS($this->officeNs, 'master-styles')
            ->item(0)
            ->getElementsByTagNameNS($this->stylesNs, 'master-page');

        foreach ($styleMasterLookup as $styleMasterSet) {
            $styleMasterName = $styleMasterSet->getAttributeNS($this->stylesNs, 'name');
            $pageLayoutName = $styleMasterSet->getAttributeNS($this->stylesNs, 'page-layout-name');
            $this->masterPrintStylesCrossReference[$styleMasterName] = $pageLayoutName;
        }
    }

    public function readStyleCrossReferences(DOMDocument $contentDom): void
    {
        $styleXReferences = $contentDom->getElementsByTagNameNS($this->officeNs, 'automatic-styles')
            ->item(0)
            ->getElementsByTagNameNS($this->stylesNs, 'style');

        foreach ($styleXReferences as $styleXreferenceSet) {
            $styleXRefName = $styleXreferenceSet->getAttributeNS($this->stylesNs, 'name');
            $stylePageLayoutName = $styleXreferenceSet->getAttributeNS($this->stylesNs, 'master-page-name');
            if (!empty($stylePageLayoutName)) {
                $this->masterStylesCrossReference[$styleXRefName] = $stylePageLayoutName;
            }
        }
    }

    public function setPrintSettingsForWorksheet(Worksheet $worksheet, string $styleName): void
    {
        if (!array_key_exists($styleName, $this->masterStylesCrossReference)) {
            return;
        }
        $masterStyleName = $this->masterStylesCrossReference[$styleName];

        if (!array_key_exists($masterStyleName, $this->masterPrintStylesCrossReference)) {
            return;
        }
        $printSettingsIndex = $this->masterPrintStylesCrossReference[$masterStyleName];

        if (!array_key_exists($printSettingsIndex, $this->pageLayoutStyles)) {
            return;
        }
        $printSettings = $this->pageLayoutStyles[$printSettingsIndex];

        $worksheet->getPageSetup()
            ->setOrientation($printSettings->orientation ?? PageSetup::ORIENTATION_DEFAULT)
            ->setPageOrder($printSettings->printOrder === 'ltr' ? PageSetup::PAGEORDER_OVER_THEN_DOWN : PageSetup::PAGEORDER_DOWN_THEN_OVER)
            ->setScale((int) trim($printSettings->scale, '%'));
    }
}

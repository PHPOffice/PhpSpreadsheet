<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Ods;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PageSettings
{
    private string $officeNs = '';

    private string $stylesNs = '';

    private string $stylesFo = '';

    private string $tableNs = '';

    /**
     * @var string[]
     */
    private array $tableStylesCrossReference = [];

    private array $pageLayoutStyles = [];

    /**
     * @var string[]
     */
    private array $masterStylesCrossReference = [];

    /**
     * @var string[]
     */
    private array $masterPrintStylesCrossReference = [];

    public function __construct(DOMDocument $styleDom)
    {
        $this->setDomNameSpaces($styleDom);
        $this->readPageSettingStyles($styleDom);
        $this->readStyleMasterLookup($styleDom);
    }

    private function setDomNameSpaces(DOMDocument $styleDom): void
    {
        $this->officeNs = (string) $styleDom->lookupNamespaceUri('office');
        $this->stylesNs = (string) $styleDom->lookupNamespaceUri('style');
        $this->stylesFo = (string) $styleDom->lookupNamespaceUri('fo');
        $this->tableNs = (string) $styleDom->lookupNamespaceUri('table');
    }

    private function readPageSettingStyles(DOMDocument $styleDom): void
    {
        $item0 = $styleDom->getElementsByTagNameNS($this->officeNs, 'automatic-styles')->item(0);
        $styles = ($item0 === null) ? [] : $item0->getElementsByTagNameNS($this->stylesNs, 'page-layout');

        foreach ($styles as $styleSet) {
            $styleName = $styleSet->getAttributeNS($this->stylesNs, 'name');
            $pageLayoutProperties = $styleSet->getElementsByTagNameNS($this->stylesNs, 'page-layout-properties')[0];
            $styleOrientation = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'print-orientation');
            $styleScale = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'scale-to');
            $stylePrintOrder = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'print-page-order');
            $centered = $pageLayoutProperties->getAttributeNS($this->stylesNs, 'table-centering');

            $marginLeft = $pageLayoutProperties->getAttributeNS($this->stylesFo, 'margin-left');
            $marginRight = $pageLayoutProperties->getAttributeNS($this->stylesFo, 'margin-right');
            $marginTop = $pageLayoutProperties->getAttributeNS($this->stylesFo, 'margin-top');
            $marginBottom = $pageLayoutProperties->getAttributeNS($this->stylesFo, 'margin-bottom');
            $header = $styleSet->getElementsByTagNameNS($this->stylesNs, 'header-style')[0];
            $headerProperties = $header->getElementsByTagNameNS($this->stylesNs, 'header-footer-properties')[0];
            $marginHeader = isset($headerProperties) ? $headerProperties->getAttributeNS($this->stylesFo, 'min-height') : null;
            $footer = $styleSet->getElementsByTagNameNS($this->stylesNs, 'footer-style')[0];
            $footerProperties = $footer->getElementsByTagNameNS($this->stylesNs, 'header-footer-properties')[0];
            $marginFooter = isset($footerProperties) ? $footerProperties->getAttributeNS($this->stylesFo, 'min-height') : null;

            $this->pageLayoutStyles[$styleName] = (object) [
                'orientation' => $styleOrientation ?: PageSetup::ORIENTATION_DEFAULT,
                'scale' => $styleScale ?: 100,
                'printOrder' => $stylePrintOrder,
                'horizontalCentered' => $centered === 'horizontal' || $centered === 'both',
                'verticalCentered' => $centered === 'vertical' || $centered === 'both',
                // margin size is already stored in inches, so no UOM conversion is required
                'marginLeft' => (float) ($marginLeft ?? 0.7),
                'marginRight' => (float) ($marginRight ?? 0.7),
                'marginTop' => (float) ($marginTop ?? 0.3),
                'marginBottom' => (float) ($marginBottom ?? 0.3),
                'marginHeader' => (float) ($marginHeader ?? 0.45),
                'marginFooter' => (float) ($marginFooter ?? 0.45),
            ];
        }
    }

    private function readStyleMasterLookup(DOMDocument $styleDom): void
    {
        $item0 = $styleDom->getElementsByTagNameNS($this->officeNs, 'master-styles')->item(0);
        $styleMasterLookup = ($item0 === null) ? [] : $item0->getElementsByTagNameNS($this->stylesNs, 'master-page');

        foreach ($styleMasterLookup as $styleMasterSet) {
            $styleMasterName = $styleMasterSet->getAttributeNS($this->stylesNs, 'name');
            $pageLayoutName = $styleMasterSet->getAttributeNS($this->stylesNs, 'page-layout-name');
            $this->masterPrintStylesCrossReference[$styleMasterName] = $pageLayoutName;
        }
    }

    public function readStyleCrossReferences(DOMDocument $contentDom): void
    {
        $item0 = $contentDom->getElementsByTagNameNS($this->officeNs, 'automatic-styles')->item(0);
        $styleXReferences = ($item0 === null) ? [] : $item0->getElementsByTagNameNS($this->stylesNs, 'style');

        foreach ($styleXReferences as $styleXreferenceSet) {
            $styleXRefName = $styleXreferenceSet->getAttributeNS($this->stylesNs, 'name');
            $stylePageLayoutName = $styleXreferenceSet->getAttributeNS($this->stylesNs, 'master-page-name');
            $styleFamilyName = $styleXreferenceSet->getAttributeNS($this->stylesNs, 'family');
            if (!empty($styleFamilyName) && $styleFamilyName === 'table') {
                $styleVisibility = 'true';
                foreach ($styleXreferenceSet->getElementsByTagNameNS($this->stylesNs, 'table-properties') as $tableProperties) {
                    $styleVisibility = $tableProperties->getAttributeNS($this->tableNs, 'display');
                }
                $this->tableStylesCrossReference[$styleXRefName] = $styleVisibility;
            }
            if (!empty($stylePageLayoutName)) {
                $this->masterStylesCrossReference[$styleXRefName] = $stylePageLayoutName;
            }
        }
    }

    public function setVisibilityForWorksheet(Worksheet $worksheet, string $styleName): void
    {
        if (!array_key_exists($styleName, $this->tableStylesCrossReference)) {
            return;
        }

        $worksheet->setSheetState(
            $this->tableStylesCrossReference[$styleName] === 'false'
                ? Worksheet::SHEETSTATE_HIDDEN
                : Worksheet::SHEETSTATE_VISIBLE
        );
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
            ->setScale((int) trim($printSettings->scale, '%'))
            ->setHorizontalCentered($printSettings->horizontalCentered)
            ->setVerticalCentered($printSettings->verticalCentered);

        $worksheet->getPageMargins()
            ->setLeft($printSettings->marginLeft)
            ->setRight($printSettings->marginRight)
            ->setTop($printSettings->marginTop)
            ->setBottom($printSettings->marginBottom)
            ->setHeader($printSettings->marginHeader)
            ->setFooter($printSettings->marginFooter);
    }
}

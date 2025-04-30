<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xml;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use SimpleXMLElement;
use stdClass;

class PageSettings
{
    /** @var (object{orientation: string, scale: ?int, printOrder: string|null,
     * paperSize: int,
     * horizontalCentered: bool, verticalCentered: bool, leftMargin: float, rightMargin: float, topMargin: float,
     * bottomMargin: float, headerMargin: float, footerMargin: float}&stdClass) */
    private stdClass $printSettings;

    public function __construct(SimpleXMLElement $xmlX)
    {
        $printSettings = $this->pageSetup($xmlX, $this->getPrintDefaults());
        $this->printSettings = $this->printSetup($xmlX, $printSettings); //* @phpstan-ignore-line
    }

    public function loadPageSettings(Spreadsheet $spreadsheet): void
    {
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize($this->printSettings->paperSize)
            ->setOrientation($this->printSettings->orientation)
            ->setScale($this->printSettings->scale)
            ->setVerticalCentered($this->printSettings->verticalCentered)
            ->setHorizontalCentered($this->printSettings->horizontalCentered)
            ->setPageOrder($this->printSettings->printOrder);
        $spreadsheet->getActiveSheet()->getPageMargins()
            ->setTop($this->printSettings->topMargin)
            ->setHeader($this->printSettings->headerMargin)
            ->setLeft($this->printSettings->leftMargin)
            ->setRight($this->printSettings->rightMargin)
            ->setBottom($this->printSettings->bottomMargin)
            ->setFooter($this->printSettings->footerMargin);
    }

    private function getPrintDefaults(): stdClass
    {
        return (object) [
            'paperSize' => 9,
            'orientation' => PageSetup::ORIENTATION_DEFAULT,
            'scale' => 100,
            'horizontalCentered' => false,
            'verticalCentered' => false,
            'printOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            'topMargin' => 0.75,
            'headerMargin' => 0.3,
            'leftMargin' => 0.7,
            'rightMargin' => 0.7,
            'bottomMargin' => 0.75,
            'footerMargin' => 0.3,
        ];
    }

    private function pageSetup(SimpleXMLElement $xmlX, stdClass $printDefaults): stdClass
    {
        if (isset($xmlX->WorksheetOptions->PageSetup)) {
            foreach ($xmlX->WorksheetOptions->PageSetup as $pageSetupData) {
                foreach ($pageSetupData as $pageSetupKey => $pageSetupValue) {
                    $pageSetupAttributes = $pageSetupValue->attributes(Namespaces::URN_EXCEL);
                    if ($pageSetupAttributes !== null) {
                        switch ($pageSetupKey) {
                            case 'Layout':
                                $this->setLayout($printDefaults, $pageSetupAttributes);

                                break;
                            case 'Header':
                                $printDefaults->headerMargin = (float) $pageSetupAttributes->Margin ?: 1.0;

                                break;
                            case 'Footer':
                                $printDefaults->footerMargin = (float) $pageSetupAttributes->Margin ?: 1.0;

                                break;
                            case 'PageMargins':
                                $this->setMargins($printDefaults, $pageSetupAttributes);

                                break;
                        }
                    }
                }
            }
        }

        return $printDefaults;
    }

    private function printSetup(SimpleXMLElement $xmlX, stdClass $printDefaults): stdClass
    {
        if (isset($xmlX->WorksheetOptions->Print)) {
            foreach ($xmlX->WorksheetOptions->Print as $printData) {
                foreach ($printData as $printKey => $printValue) {
                    switch ($printKey) {
                        case 'LeftToRight':
                            $printDefaults->printOrder = PageSetup::PAGEORDER_OVER_THEN_DOWN;

                            break;
                        case 'PaperSizeIndex':
                            $printDefaults->paperSize = (int) $printValue ?: 9;

                            break;
                        case 'Scale':
                            $printDefaults->scale = (int) $printValue ?: 100;

                            break;
                    }
                }
            }
        }

        return $printDefaults;
    }

    private function setLayout(stdClass $printDefaults, SimpleXMLElement $pageSetupAttributes): void
    {
        $printDefaults->orientation = (string) strtolower($pageSetupAttributes->Orientation ?? '') ?: PageSetup::ORIENTATION_PORTRAIT;
        $printDefaults->horizontalCentered = (bool) $pageSetupAttributes->CenterHorizontal ?: false;
        $printDefaults->verticalCentered = (bool) $pageSetupAttributes->CenterVertical ?: false;
    }

    private function setMargins(stdClass $printDefaults, SimpleXMLElement $pageSetupAttributes): void
    {
        $printDefaults->leftMargin = (float) $pageSetupAttributes->Left ?: 1.0;
        $printDefaults->rightMargin = (float) $pageSetupAttributes->Right ?: 1.0;
        $printDefaults->topMargin = (float) $pageSetupAttributes->Top ?: 1.0;
        $printDefaults->bottomMargin = (float) $pageSetupAttributes->Bottom ?: 1.0;
    }
}

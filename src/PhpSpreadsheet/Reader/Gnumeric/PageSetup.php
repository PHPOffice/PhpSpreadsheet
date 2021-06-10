<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup as WorksheetPageSetup;
use SimpleXMLElement;

class PageSetup
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function printInformation(SimpleXMLElement $sheet): self
    {
        if (isset($sheet->PrintInformation)) {
            $printInformation = $sheet->PrintInformation[0];
            $scale = (string) $printInformation->Scale->attributes()['percentage'];
            $pageOrder = (string) $printInformation->order;
            $orientation = (string) $printInformation->orientation;
            $horizontalCentered = (string) $printInformation->hcenter->attributes()['value'];
            $verticalCentered = (string) $printInformation->vcenter->attributes()['value'];

            $this->spreadsheet->getActiveSheet()->getPageSetup()
                ->setPageOrder($pageOrder === 'r_then_d' ? WorksheetPageSetup::PAGEORDER_OVER_THEN_DOWN : WorksheetPageSetup::PAGEORDER_DOWN_THEN_OVER)
                ->setScale((int) $scale)
                ->setOrientation($orientation ?? WorksheetPageSetup::ORIENTATION_DEFAULT)
                ->setHorizontalCentered((bool) $horizontalCentered)
                ->setVerticalCentered((bool) $verticalCentered);
        }

        return $this;
    }

    public function sheetMargins(SimpleXMLElement $sheet): self
    {
        if (isset($sheet->PrintInformation, $sheet->PrintInformation->Margins)) {
            $marginSet = [
                // Default Settings
                'top' => 0.75,
                'header' => 0.3,
                'left' => 0.7,
                'right' => 0.7,
                'bottom' => 0.75,
                'footer' => 0.3,
            ];

            $marginSet = $this->buildMarginSet($sheet, $marginSet);
            $this->adjustMargins($marginSet);
        }

        return $this;
    }

    private function buildMarginSet(SimpleXMLElement $sheet, array $marginSet): array
    {
        foreach ($sheet->PrintInformation->Margins->children(Gnumeric::NAMESPACE_GNM) as $key => $margin) {
            $marginAttributes = $margin->attributes();
            $marginSize = ($marginAttributes['Points']) ?? 72; //    Default is 72pt
            // Convert value in points to inches
            $marginSize = PageMargins::fromPoints((float) $marginSize);
            $marginSet[$key] = $marginSize;
        }

        return $marginSet;
    }

    private function adjustMargins(array $marginSet): void
    {
        foreach ($marginSet as $key => $marginSize) {
            // Gnumeric is quirky in the way it displays the header/footer values:
            //    header is actually the sum of top and header; footer is actually the sum of bottom and footer
            //    then top is actually the header value, and bottom is actually the footer value
            switch ($key) {
                case 'left':
                case 'right':
                    $this->sheetMargin($key, $marginSize);

                    break;
                case 'top':
                    $this->sheetMargin($key, $marginSet['header'] ?? 0);

                    break;
                case 'bottom':
                    $this->sheetMargin($key, $marginSet['footer'] ?? 0);

                    break;
                case 'header':
                    $this->sheetMargin($key, ($marginSet['top'] ?? 0) - $marginSize);

                    break;
                case 'footer':
                    $this->sheetMargin($key, ($marginSet['bottom'] ?? 0) - $marginSize);

                    break;
            }
        }
    }

    private function sheetMargin(string $key, float $marginSize): void
    {
        switch ($key) {
            case 'top':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setTop($marginSize);

                break;
            case 'bottom':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setBottom($marginSize);

                break;
            case 'left':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setLeft($marginSize);

                break;
            case 'right':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setRight($marginSize);

                break;
            case 'header':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setHeader($marginSize);

                break;
            case 'footer':
                $this->spreadsheet->getActiveSheet()->getPageMargins()->setFooter($marginSize);

                break;
        }
    }
}

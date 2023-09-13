<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup as WorksheetPageSetup;
use SimpleXMLElement;

class PageSetup
{
    private Spreadsheet $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    public function printInformation(SimpleXMLElement $sheet): self
    {
        if (isset($sheet->PrintInformation, $sheet->PrintInformation[0])) {
            $printInformation = $sheet->PrintInformation[0];
            $setup = $this->spreadsheet->getActiveSheet()->getPageSetup();

            $attributes = $printInformation->Scale->attributes();
            if (isset($attributes['percentage'])) {
                $setup->setScale((int) $attributes['percentage']);
            }
            $pageOrder = (string) $printInformation->order;
            if ($pageOrder === 'r_then_d') {
                $setup->setPageOrder(WorksheetPageSetup::PAGEORDER_OVER_THEN_DOWN);
            } elseif ($pageOrder === 'd_then_r') {
                $setup->setPageOrder(WorksheetPageSetup::PAGEORDER_DOWN_THEN_OVER);
            }
            $orientation = (string) $printInformation->orientation;
            if ($orientation !== '') {
                $setup->setOrientation($orientation);
            }
            $attributes = $printInformation->hcenter->attributes();
            if (isset($attributes['value'])) {
                $setup->setHorizontalCentered((bool) (string) $attributes['value']);
            }
            $attributes = $printInformation->vcenter->attributes();
            if (isset($attributes['value'])) {
                $setup->setVerticalCentered((bool) (string) $attributes['value']);
            }
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

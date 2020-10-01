<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Html;

use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HtmlHelper
{
    public static function createHtml(string $html): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($filename, $html);

        return $filename;
    }

    public static function loadHtmlIntoSpreadsheet(string $filename, bool $unlink = false): Spreadsheet
    {
        $html = new Html();
        $spreadsheet = $html->load($filename);
        if ($unlink) {
            unlink($filename);
        }

        return $spreadsheet;
    }
}

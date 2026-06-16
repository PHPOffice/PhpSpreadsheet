<?php

require __DIR__ . '/../Header.php';

use PhpOffice\PhpSpreadsheet\Helper\Sample;

function datesToIso8601(string $infile, Sample $helper): void
{
    $zip = new ZipArchive();
    if ($zip->open($infile) !== true) {
        throw new Exception("unable to open zip $infile");
    }
    $files = ['content.xml', 'styles.xml'];
    $modified = false;
    foreach ($files as $file) {
        $data = $zip->getFromName($file);
        if ($data === false) {
            throw new Exception("unable to read member $file");
        }
        $newData = str_replace('<number:year/>', '<number:year number:style="long"/>', $data);
        $newData = preg_replace(
            '~'
            . '(<number:day(?: number:style="long")?/>)'
            . '<number:text>[/-]</number:text>'
            . '(<number:month(?: number:style="long")?/>)'
            . '<number:text>[/-]</number:text>'
            . '(<number:year number:style="long"/>)'
            . '~',
            '$3<number:text>-</number:text>$2'
            . '<number:text>-</number:text>$1',
            $newData
        ) ?? $newData;
        $newData = preg_replace(
            '~'
            . '(<number:month(?: number:style="long")?/>)'
            . '<number:text>[/-]</number:text>'
            . '(<number:day(?: number:style="long")?/>)'
            . '<number:text>[/-]</number:text>'
            . '(<number:year number:style="long"/>)'
            . '~',
            '$3<number:text>-</number:text>$1'
            . '<number:text>-</number:text>$2',
            $newData
        ) ?? $newData;
        $newData = preg_replace(
            '~'
            . '(<number:date-style [^>]*)'
            . ' number:automatic-order="true"'
            . '~',
            '$1',
            $newData
        ) ?? $newData;
        if ($data === $newData) {
            $helper->log("no changes needed for $file");
        } else {
            $zip->deleteName($file);
            $zip->addFromString($file, $newData);
            $helper->log("modified $file");
            $modified = true;
        }
    }
    $zip->close();
    if ($modified) {
        $helper->log("Modified $infile");
    } else {
        $helper->log("No modifications to $infile");
    }
}

$infileBase = '56_MixedDateFormats.ods';
$infileBase1 = __DIR__ . '/../templates/' . $infileBase;
$infile = realpath($infileBase1);
if ($infile === false) {
    throw new Exception("Unable to locate $infileBase1");
}

/** @var Sample $helper */
$helper->log("Infile is $infile");
$outDirectory = $helper->getTemporaryFolder();
$helper->log("outDirectory is $outDirectory");
$outfile = $outDirectory . '/56_OdsToISO8601.ods';
$helper->log("Outfile is $outfile");
$helper->log('Attempting copy');
if (!copy($infile, $outfile)) {
    throw new Exception('Copy failed');
}

$helper->log('Update date formatting xml');
datesToIso8601($outfile, $helper);

$helper->addDownloadLink($outfile);

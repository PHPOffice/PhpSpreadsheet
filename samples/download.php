<?php

require_once __DIR__ . '/Bootstrap.php';

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

$filename = basename(StringHelper::convertToString($_GET['name'] ?? ''));
$filetype = StringHelper::convertToString($_GET['type'] ?? '');

try {
    $downloader = new Downloader((new Sample())->getTemporaryFolder(), $filename, $filetype);
    $downloader->download();
} catch (Exception $e) {
    exit($e->getMessage());
}

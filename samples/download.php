<?php

require_once __DIR__ . '/Bootstrap.php';

use PhpOffice\PhpSpreadsheet\Helper\Downloader;
use PhpOffice\PhpSpreadsheet\Helper\Sample;

$filename = basename($_GET['name']);
$filetype = $_GET['type'];

try {
    $downloader = new Downloader((new Sample())->getTemporaryFolder(), $filename, $filetype);
    $downloader->download();
} catch (Exception $e) {
    exit($e->getMessage());
}

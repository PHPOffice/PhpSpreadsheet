<?php

require_once __DIR__ . '/Header.php';

$requirements = [
    'PHP 8.2' => version_compare(PHP_VERSION, '8.2', '>='),
    'PHP extension ctype' => extension_loaded('ctype'),
    'PHP extension dom' => extension_loaded('dom'),
    'PHP extension fileinfo' => extension_loaded('fileinfo'),
    'PHP extension filter' => extension_loaded('filter'),
    'PHP extension gd' => extension_loaded('gd'),
    'PHP extension iconv' => extension_loaded('iconv'),
    'PHP extension intl (optional)' => extension_loaded('intl'),
    'PHP extension libxml' => extension_loaded('libxml'),
    'PHP extension mbstring' => extension_loaded('mbstring'),
    'PHP extension openssl (optional)' => extension_loaded('openssl'),
    'PHP extension simplexml' => extension_loaded('simplexml'),
    'PHP extension xml' => extension_loaded('xml'),
    'PHP extension xmlreader' => extension_loaded('xmlreader'),
    'PHP extension xmlwriter' => extension_loaded('xmlwriter'),
    'PHP extension zip' => extension_loaded('zip'),
    'PHP extension zlib' => extension_loaded('zlib'),
];

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
if (!$helper->isCli()) {
    ?>
    <div class="p-5 mb-4 bg-body-tertiary rounded-3">
        <p class="fs-5">Welcome to PHPSpreadsheet, a library written in pure PHP and providing a set of classes that allow you to read from and to write to different spreadsheet file formats, like Excel and LibreOffice Calc.</p>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-lg btn-primary" href="https://github.com/PHPOffice/PHPSpreadsheet" role="button"><i class="fa fa-github fa-lg" title="GitHub"></i>  Fork us on Github!</a>
            <a class="btn btn-lg btn-primary" href="https://phpspreadsheet.readthedocs.io" role="button"><i class="fa fa-book fa-lg" title="Docs"></i>  Read the Docs</a>
        </div>
    </div>
    <?php
    echo '<h1>Requirement check</h1>';
    echo '<ul>';
    foreach ($requirements as $label => $result) {
        $status = $result ? 'passed' : 'failed';
        echo "<li>{$label} ... <span class='{$status}'>{$status}</span></li>";
    }
    echo '</ul>';
    $helper->endWebPage();
} else {
    echo 'Requirement check:' . PHP_EOL;
    foreach ($requirements as $label => $result) {
        $status = $result ? '32m passed' : '31m failed';
        echo "{$label} ... \033[{$status}\033[0m" . PHP_EOL;
    }
}

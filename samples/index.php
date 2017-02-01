<?php

require_once 'Header.php';

$requirements = [
    'PHP 5.6.0' => version_compare(phpversion(), '5.6.0', '>='),
    'PHP extension XML' => extension_loaded('xml'),
    'PHP extension xmlwriter' => extension_loaded('xmlwriter'),
    'PHP extension mbstring' => extension_loaded('mbstring'),
    'PHP extension ZipArchive (optional)' => extension_loaded('zip'),
    'PHP extension GD (optional)' => extension_loaded('gd'),
    'PHP extension dom (optional)' => extension_loaded('dom'),
];

if (!$helper->isCli()) {
    ?>
    <div class="jumbotron">
        <p>Welcome to PHPSpreadsheet, a library written in pure PHP and providing a set of classes that allow you to read from and to write to different spreadsheet file formats, like Excel and LibreOffice Calc.</p>
        <p>&nbsp;</p>
        <p>
            <a class="btn btn-lg btn-primary" href="https://github.com/PHPOffice/PHPSpreadsheet" role="button"><i class="fa fa-github fa-lg" title="GitHub"></i>  Fork us on Github!</a>
            <a class="btn btn-lg btn-primary" href="http://phpspreadsheet.readthedocs.org/en/develop/" role="button"><i class="fa fa-book fa-lg" title="Docs"></i>  Read the Docs</a>
        </p>
    </div>
    <?php
    echo '<h3>Requirement check:</h3>';
    echo '<ul>';
    foreach ($requirements as $label => $result) {
        $status = $result ? 'passed' : 'failed';
        echo "<li>{$label} ... <span class='{$status}'>{$status}</span></li>";
    }
    echo '</ul>';
} else {
    echo 'Requirement check:' . PHP_EOL;
    foreach ($requirements as $label => $result) {
        $status = $result ? '32m passed' : '31m failed';
        echo "{$label} ... \033[{$status}\033[0m" . PHP_EOL;
    }
}

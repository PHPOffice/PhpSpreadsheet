<?php

/**
 * Header file.
 */

use PhpOffice\PhpSpreadsheet\Helper\Sample;

error_reporting(E_ALL);

require_once __DIR__ . '/Bootstrap.php';

$helper = new Sample();

// Return to the caller script when runs by CLI
if ($helper->isCli()) {
    return;
}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<head>
    <title><?php echo $helper->getPageTitle(); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css?v5"/>
    <link rel="stylesheet" href="/bootstrap/css/font-awesome.v4.used.css"/>
    <link rel="stylesheet" href="/bootstrap/css/phpspreadsheet.css?v5"/>
    <script src="/bootstrap/js/bootstrap.bundle.min.js" defer></script>
    <script src="/bootstrap/js/darklighttoggle.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar navbar-expand-lg bg-body-tertiary" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
                        <span class="visually-hidden">Toggle navigation</span>
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <a class="navbar-brand" href="/">PHPSpreadsheet</a>
                    <br><a href="https://github.com/PHPOffice/PHPSpreadsheet" style="text-decoration:none;">
                            <i class="fa fa-github fa-lg" title="GitHub"></i>
                          </a>
                          <a href="https://phpspreadsheet.readthedocs.io" style="text-decoration:none; margin-left: 1em;">
                            <i class="fa fa-book fa-lg" title="Docs"></i>
                          </a>
<button class="btn btn-outline-primary btn-sm" id="themeToggler" type="button" title="Toggle dark/light mode" style="margin-left:1em; border:none">
    <i class="fa fa-moon-o fa-lg" id="themeIcon"></i>
</button>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav"><?php
                    foreach ($helper->getSamples() as $category => $files) {
                        ?><li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"><?php echo $category ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end text-wrap" style="max-width: 300px;"><?php
                            foreach ($files as $name => $file) {
                                if (
                                    !in_array(
                                        $file,
                                        [
                                            'Chart/35_Chart_render.php',
                                            'Chart/35_Chart_render33.php',
                                        ],
                                        true
                                    )
                                ) {
                                    echo '<li><a class="dropdown-item" href="/' . $file . '">' . $name . '</a></li>';
                                }
                            } ?></ul>
                        </li>
                        <?php
                    } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        echo $helper->getPageHeading();

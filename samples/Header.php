<?php
/**
 * Header file.
 */
use PhpOffice\PhpSpreadsheet\Helper\Sample;

error_reporting(E_ALL);

require_once __DIR__ . '/../src/Bootstrap.php';

$helper = new Sample();
if (!defined('EOL')) {
    define('EOL', $helper->isCli() ? PHP_EOL : '<br />');
}

// Return to the caller script when runs by CLI
if ($helper->isCli()) {
    return;
}
?>
<title><?php echo $helper->getPageTitle(); ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="bootstrap/css/font-awesome.min.css" />
<link rel="stylesheet" href="bootstrap/css/phpspreadsheet.css" />
<script src="bootstrap/js/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">PHPSpreadsheet</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown active">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-code fa-lg"></i>&nbsp;Samples<strong class="caret"></strong></a>
                            <ul class="dropdown-menu"><?php
                            foreach ($helper->getSamples() as $name => $file) {
                                echo '<li><a href="' . basename($file) . '">' . $name . '</a></li>';
                            }
                                ?></ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="https://github.com/PHPOffice/PHPSpreadsheet"><i class="fa fa-github fa-lg" title="GitHub"></i>&nbsp;</a></li>
                        <li><a href="https://phpspreadsheet.readthedocs.io"><i class="fa fa-book fa-lg" title="Docs"></i>&nbsp;</a></li>
                        <li><a href="http://twitter.com/PHPOffice"><i class="fa fa-twitter fa-lg" title="Twitter"></i>&nbsp;</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
        echo $helper->getPageHeading();

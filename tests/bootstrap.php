<?php
/**
 * @copyright   Copyright (C) 2011-2014 PhpSpreadsheet. All rights reserved.
 * @author      Mark Baker
 */
chdir(__DIR__);

setlocale(LC_ALL, 'en_US.utf8');

// PHP 5.3 Compat
date_default_timezone_set('Europe/London');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../src'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, [
    realpath(APPLICATION_PATH . '/../src'),
    './',
    __DIR__,
    get_include_path(),
]));

if (!defined('PHPSPREADSHEET_ROOT')) {
    define('PHPSPREADSHEET_ROOT', APPLICATION_PATH . '/');
}

require_once PHPSPREADSHEET_ROOT . 'Bootstrap.php';

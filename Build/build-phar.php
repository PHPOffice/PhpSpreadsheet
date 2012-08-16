<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */

# required: PHP 5.3+ and zlib extension

// ini option check
if (ini_get('phar.readonly')) {
	echo "php.ini: set the 'phar.readonly' option to 0 to enable phar creation\n";
	exit(1);
}

// output name
$pharName = 'PHPExcel.phar';

// target folder
$sourceDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR;

// default meta information
$metaData = array(
	'Author'		=> 'Mark Baker <mbaker@inviqa.com>',
	'Description'	=> 'A pure PHP library for reading and writing spreadsheet files',
	'Copyright'		=> 'Mark Baker (c) 2006-' . date('Y'),
	'Timestamp'		=> time(),
	'Version'		=> '##VERSION##',
	'Date'			=> '##DATE##'
);

// cleanup
if (file_exists($pharName)) {
	echo "Removed: {$pharName}\n";
	unlink($pharName);
}

echo "Building...\n";

// the phar object
$phar = new Phar($pharName, null, 'PHPExcel');
$phar->buildFromDirectory($sourceDir);
$phar->setStub(
<<<'EOT'
<?php
	spl_autoload_register(function ($class) {
		include 'phar://PHPExcel/' . str_replace('_', '/', $class) . '.php';
	});

	try {
		Phar::mapPhar();
		include 'phar://PHPExcel/PHPExcel.php';
	} catch (PharException $e) {
		error_log($e->getMessage());
		exit(1);
	}

	__HALT_COMPILER();
EOT
);
$phar->setMetadata($metaData);
$phar->compressFiles(Phar::GZ);

echo "Complete.\n";

exit();

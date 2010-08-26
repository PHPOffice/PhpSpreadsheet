<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/**
 * This file creates a build of PHPExcel
 */

// Build parameters
$sVersion	= "";
$sDate		= "";

// Read build parameters from STDIN
$stdin = fopen("php://stdin", 'r');
echo "PHPExcel build script\n";
echo "---------------------\n";
echo "Enter the version number you want to add to the build:\t\t\t";
$sVersion	= rtrim(fread($stdin, 1024));

echo "Enter the date number you want to add to the build: (YYYY-MM-DD)\t";
$sDate 		= rtrim(fread($stdin, 1024));

echo "\n\n";

// Specify paths and files to include
$aFilesToInclude 	= array('../changelog.txt', '../install.txt', '../license.txt');
$aPathsToInclude 	= array('../Classes', '../Tests', '../Documentation');
$aIgnorePatterns 	= array('/\.svn/i', '/\.settings/i', '/\.project/i', '/\.projectOptions/i', '/\.cache/i', '/assets/i');
$sClassPath		 	= '../Classes';
$sPEARPath		 	= 'C:\php\5.2.9\pear';
$sAPIDocumentation	= '../Documentation/API/';

// Create API documentation folder and tell to create documentation
@mkdir($sAPIDocumentation);
echo "Please, generate API documentation using phpDocumentor.\r\n";

$finished = '';
while (strtolower($finished) != 'y') {
	$finished = '';
	echo "Has documentation generation finished? (y/n)\t";
	$finished	= rtrim(fread($stdin, 1024));
}

echo "\n\n\n";

// Resulting file
$strResultingFile = $sVersion . '.zip';

// Starting build
echo date('H:i:s') . " Starting build...\n";

// Create new ZIP file and open it for writing
echo date('H:i:s') . " Creating ZIP archive...\n";
$objZip = new ZipArchive();
			
// Try opening the ZIP file
if ($objZip->open($strResultingFile, ZIPARCHIVE::OVERWRITE) !== true) {
	throw new Exeption("Could not open " . $strResultingFile . " for writing!");
}

// Add files to include
foreach ($aFilesToInclude as $strFile) {
	echo date('H:i:s') . " Adding file $strFile\n";
	addFileToZIP($strFile, $objZip, $sVersion, $sDate);
}

// Add paths to include
foreach ($aPathsToInclude as $strPath) {
	addPathToZIP($strPath, $objZip, $sVersion, $sDate);
}

// Set archive comment...
echo date('H:i:s') . " Set archive comment...\n";
$objZip->setArchiveComment('PHPExcel - http://www.codeplex.com/PHPExcel');

// Close file
echo date('H:i:s') . " Saving ZIP archive...\n";
$objZip->close();

// Copy classes directory
echo date('H:i:s') . " Copying class directory...\n";
mkdir('./tmp');
dircopy($sClassPath, './tmp');

// Create PEAR package.xml
echo date('H:i:s') . " Creating PEAR package.xml...\n";
$packageFile = file_get_contents('package.xml');
$packageFile = replaceMetaData($packageFile, $sVersion, $sDate);

$packageFile = str_replace('##PEAR_DIR##', addPathToPEAR('./tmp', '', $sVersion, $sDate), $packageFile);
$fh = fopen('./tmp/package.xml', 'w');
fwrite($fh, $packageFile);
fclose($fh);

// Create PEAR package
echo date('H:i:s') . " Creating PEAR package...\n";
echo shell_exec("$sPEARPath package ./tmp/package.xml");

// Wait a minute (TortoiseSVN on USB stick is slow!)
echo date('H:i:s') . " Waiting...\n";
sleep(120);

// Clean temporary files
echo date('H:i:s') . " Cleaning temporary files...\n";
unlink('./tmp/package.xml');
rm('./tmp');

// Finished build
echo date('H:i:s') . " Finished build!\n";
fclose($stdin);

/**
 * Add a specific path's files and folders to a ZIP object
 *
 * @param string 		$strPath		Path to add
 * @param ZipArchive 	$objZip			ZipArchive object
 * @param string		$strVersion		Version string
 * @param string		$strDate		Date string
 */
function addPathToZIP($strPath, $objZip, $strVersion, $strDate) {
	global $aIgnorePatterns;
	
	echo date('H:i:s') . " Adding path $strPath...\n";
	
	$currentDir = opendir($strPath);
	while ($strFile = readdir($currentDir)) {
		if ($strFile != '.' && $strFile != '..') {
			if (is_file($strPath . '/' . $strFile)) {
				addFileToZIP($strPath . '/' . $strFile, $objZip, $strVersion, $strDate);
			} else if (is_dir($strPath . '/' . $strFile)) {
				if (!shouldIgnore($strFile)) {
					addPathToZIP( ($strPath . '/' . $strFile), $objZip, $strVersion, $strDate );
				}
			}
		}
	}
}

/**
 * Add a specific file to ZIP
 *
 * @param string 		$strFile		File to add
 * @param ZipArchive 	$objZip			ZipArchive object
 * @param string		$strVersion		Version string
 * @param string		$strDate		Date string
 */
function addFileToZIP($strFile, $objZip, $strVersion, $strDate) {
	if (!shouldIgnore($strFile)) {
		$fileContents = file_get_contents($strFile);
		$fileContents = replaceMetaData($fileContents, $strVersion, $strDate);
		
		//$objZip->addFile($strFile, cleanFileName($strFile));
		$objZip->addFromString( cleanFileName($strFile), $fileContents );
	}
}

/**
 * Cleanup a filename
 *
 * @param 	string	$strFile			Filename
 * @return	string	Filename
 */
function cleanFileName($strFile) {
	 $strFile = str_replace('../', '', $strFile);
	 $strFile = str_replace('WINDOWS', '', $strFile);
	 
	 while (preg_match('/\/\//i', $strFile)) {
	 	$strFile = str_replace('//', '/', $strFile);
	 }
	 
	 return $strFile;
}

/**
 * Replace metadata in string
 *
 * @param string 		$strString		String contents
 * @param string		$strVersion		Version string
 * @param string		$strDate		Date string
 * @return string		String contents
 */
function replaceMetaData($strString, $strVersion, $strDate) {
	$strString = str_replace('##VERSION##', $strVersion, $strString);
	$strString = str_replace('##DATE##', $strDate, $strString);
	return $strString;
}

/**
 * Add a specific path's files and folders to a PEAR dir list
 *
 * @param 	string 		$strPath		Path to add
 * @param 	string 		$strPEAR		String containing PEAR dir definitions
 * @param 	string		$strVersion		Version string
 * @param 	string		$strDate		Date string
 * @return 	string		String containing PEAR dir definitions
 */
function addPathToPEAR($strPath, $strPEAR, $strVersion, $strDate) {     
	global $aIgnorePatterns;
	  
	$currentDir = opendir($strPath);
	while ($strFile = readdir($currentDir)) {
		if ($strFile != '.' && $strFile != '..') {
			if (is_file($strPath . '/' . $strFile) && !preg_match('/package.xml/i', $strFile)) {
				$strPEAR .= addFileToPEAR($strPath . '/' . $strFile, '', $strVersion, $strDate);
			} else if (is_dir($strPath . '/' . $strFile)) {
				if (!shouldIgnore($strFile)) {
					$strPEAR .= '<dir name="' . $strFile . '">';
					$strPEAR .= addPathToPEAR( ($strPath . '/' . $strFile), '', $strVersion, $strDate );
					$strPEAR .= '</dir>';
				}
			}
		}
	}
	
	return $strPEAR;
}

/**
 * Add a specific file to a PEAR dir list
 *
 * @param 	string 		$strFile		File to add
 * @param 	string 		$strPEAR		String containing PEAR dir definitions
 * @param 	string		$strVersion		Version string
 * @param 	string		$strDate		Date string
 * @return 	string		String containing PEAR dir definitions
 */
function addFileToPEAR($strFile, $strPEAR, $strVersion, $strDate) {
	if (!shouldIgnore($strFile)) {
		$fileContents = file_get_contents($strFile);
		$fileContents = replaceMetaData($fileContents, $strVersion, $strDate);
		$fh = fopen($strFile, 'w');
		fwrite($fh, $fileContents);
		fclose($fh);
		
		$strPEAR .= '<file name="' . basename($strFile) . '" role="php" />';
		
		return $strPEAR;
	} else {
		return '';
	}
}

/**
 * Copy a complete directory
 *
 * @param  string	$srcdir		Source directory
 * @param  string	$dstdir		Destination directory
 * @return int		Number of copied files
 */
function dircopy($srcdir, $dstdir, $verbose = false) {	
  $num = 0;
  if(!is_dir($dstdir) && !shouldIgnore($dstdir)) mkdir($dstdir);
  if($curdir = opendir($srcdir)) {
    while($file = readdir($curdir)) {
      if($file != '.' && $file != '..') {
        $srcfile = $srcdir . '\\' . $file;
        $dstfile = $dstdir . '\\' . $file;
        if(is_file($srcfile)  && !shouldIgnore($srcfile)) {
          if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
          if($ow > 0) {
            if($verbose) echo "Copying '$srcfile' to '$dstfile'...";
            if(copy($srcfile, $dstfile)) {
              touch($dstfile, filemtime($srcfile)); $num++;
              if($verbose) echo "OK\n";
            }
            else echo "Error: File '$srcfile' could not be copied!\n";
          }                  
        }
        else if(is_dir($srcfile) && !shouldIgnore($srcfile)) {
          $num += dircopy($srcfile, $dstfile, $verbose);
        }
      }
    }
    closedir($curdir);
  }
  return $num;
}

/**
 * rm() -- Very Vigorously erase files and directories. Also hidden files !!!!
 *
 * @param $dir string
 *                   be carefull to:
 *                         if($obj=='.' || $obj=='..') continue;
 *                    if not it will erase all the server...it happened to me ;)
 *                     the function is permission dependent.    
 */
function rm($dir) {
    if(!$dh = @opendir($dir)) return;
    while (($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        @chmod($dir.'/'.$obj, 0777);
        if (!@unlink($dir.'/'.$obj)) rm($dir.'/'.$obj);
    }
   @rmdir($dir);
   @shell_exec('rmdir /S /Q "' . $dir . '"');
}

/**
 * Should a file/folder be ignored?
 *
 * @param 	string	$pName
 * @return 	boolean
 */
function shouldIgnore($pName = '') {
	global $aIgnorePatterns;
	
	$ignore = false;
	foreach ($aIgnorePatterns as $ignorePattern) {
		if (preg_match($ignorePattern, $pName)) {
			$ignore = true;
		}
	}
	return $ignore;
}
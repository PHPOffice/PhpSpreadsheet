<?php
/**
* Script to create REGRESS package for download
*
* @author Mike Bommarito
* @author Paul Meagher
* @version 0.3
* @modified Apr 2, 2006
*
* Note: Script requires the PEAR Archive_Tar package be installed:
*
* @see http://pear.php.net/package/Archive_Tar
*/

// name and directory of package
$pkgName   = "JAMA";

// root of PHP/Math build directory
$buildDir  = substr(dirname(__FILE__), 0, -5 - strlen($pkgName));

// switch to PHP/Math build directory
chdir($buildDir);

$tarName = "$pkgName.tar.gz";  

$tarPath = $buildDir.$pkgName."/downloads/".$tarName;

if($_GET['op'] == "download") {  
  
	require_once('Archive/Tar.php');  
	
	$tar   = new Archive_Tar($tarPath);

  // create $pkgName archive under $pkgName folder
  $files = glob("$pkgName/*.php");
  $files = array_merge($files, glob("$pkgName/*.TXT"));
  $files = array_merge($files, glob("$pkgName/docs/*.php"));
  $files = array_merge($files, glob("$pkgName/docs/includes/*.php"));
  $files = array_merge($files, glob("$pkgName/examples/*.php"));
  $files = array_merge($files, glob("$pkgName/tests/*.php"));  
  $files = array_merge($files, glob("$pkgName/utils/*.php"));    
  
	$tar->create($files);
		
	// create the download url
  $webDir  = substr($_SERVER['PHP_SELF'], 0, -18);
  $urlPath = "http://".$_SERVER['HTTP_HOST'].$webDir."/downloads";
  
  // redirect to download url
	header("Location: $urlPath/$tarName");

}

include_once "includes/header.php";
include_once "includes/navbar.php";
?>
<p>
Download current version: 
</p>
<ul>
 <li><a href='<?php echo $_SERVER['PHP_SELF']."?op=download"; ?>'><?php echo $tarName ?></a></li>
</ul>
<?php
include_once "includes/footer.php";
?>

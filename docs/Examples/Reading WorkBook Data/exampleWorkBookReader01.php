<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reading WorkBook Data Example #01</title>

</head>
<body>

<h1>PHPExcel Reading WorkBook Data Example #01</h1>
<h2>Read the WorkBook Properties</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** \PhpOffice\PhpSpreadsheet\IOFactory */
include 'PHPExcel/IOFactory.php';

$inputFileType = 'Xls';
$inputFileName = './sampleData/example1.xls';

/*  Create a new Reader of the type defined in $inputFileType  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/*  Load $inputFileName to a PHPExcel Object  **/
$spreadsheet = $reader->load($inputFileName);

echo '<hr />';

/*  Read the document's creator property  **/
$creator = $spreadsheet->getProperties()->getCreator();
echo '<b>Document Creator: </b>',$creator,'<br />';

/*  Read the Date when the workbook was created (as a PHP timestamp value)  **/
$creationDatestamp = $spreadsheet->getProperties()->getCreated();
/*  Format the date and time using the standard PHP date() function  **/
$creationDate = date('l, d<\s\up>S</\s\up> F Y', $creationDatestamp);
$creationTime = date('g:i A', $creationDatestamp);
echo '<b>Created On: </b>',$creationDate,' at ',$creationTime,'<br />';

/*  Read the name of the last person to modify this workbook  **/
$modifiedBy = $spreadsheet->getProperties()->getLastModifiedBy();
echo '<b>Last Modified By: </b>',$modifiedBy,'<br />';

/*  Read the Date when the workbook was last modified (as a PHP timestamp value)  **/
$modifiedDatestamp = $spreadsheet->getProperties()->getModified();
/*  Format the date and time using the standard PHP date() function  **/
$modifiedDate = date('l, d<\s\up>S</\s\up> F Y', $modifiedDatestamp);
$modifiedTime = date('g:i A', $modifiedDatestamp);
echo '<b>Last Modified On: </b>',$modifiedDate,' at ',$modifiedTime,'<br />';

/*  Read the workbook title property  **/
$workbookTitle = $spreadsheet->getProperties()->getTitle();
echo '<b>Title: </b>',$workbookTitle,'<br />';

/*  Read the workbook description property  **/
$description = $spreadsheet->getProperties()->getDescription();
echo '<b>Description: </b>',$description,'<br />';

/*  Read the workbook subject property  **/
$subject = $spreadsheet->getProperties()->getSubject();
echo '<b>Subject: </b>',$subject,'<br />';

/*  Read the workbook keywords property  **/
$keywords = $spreadsheet->getProperties()->getKeywords();
echo '<b>Keywords: </b>',$keywords,'<br />';

/*  Read the workbook category property  **/
$category = $spreadsheet->getProperties()->getCategory();
echo '<b>Category: </b>',$category,'<br />';

/*  Read the workbook company property  **/
$company = $spreadsheet->getProperties()->getCompany();
echo '<b>Company: </b>',$company,'<br />';

/*  Read the workbook manager property  **/
$manager = $spreadsheet->getProperties()->getManager();
echo '<b>Manager: </b>',$manager,'<br />';

?>
<body>
</html>

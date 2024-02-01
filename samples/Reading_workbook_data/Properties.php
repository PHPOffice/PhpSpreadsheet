<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';

// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);
// Load $inputFileName to a PhpSpreadsheet Object
$spreadsheet = $reader->load($inputFileName);

// Read the document's creator property
$creator = $spreadsheet->getProperties()->getCreator();
$helper->log('<b>Document Creator: </b>' . $creator);

// Read the Date when the workbook was created (as a PHP timestamp value)
$creationDatestamp = $spreadsheet->getProperties()->getCreated();
$creationDate = Date::formattedDateTimeFromTimestamp("$creationDatestamp", 'l, d<\s\up>S</\s\up> F Y');
$creationTime = Date::formattedDateTimeFromTimestamp("$creationDatestamp", 'g:i A');
$helper->log('<b>Created On: </b>' . $creationDate . ' at ' . $creationTime);

// Read the name of the last person to modify this workbook
$modifiedBy = $spreadsheet->getProperties()->getLastModifiedBy();
$helper->log('<b>Last Modified By: </b>' . $modifiedBy);

// Read the Date when the workbook was last modified (as a PHP timestamp value)
$modifiedDatestamp = $spreadsheet->getProperties()->getModified();
// Format the date and time using the standard PHP date() function
$modifiedDate = Date::formattedDateTimeFromTimestamp("$modifiedDatestamp", 'l, d<\s\up>S</\s\up> F Y');
$modifiedTime = Date::formattedDateTimeFromTimestamp("$modifiedDatestamp", 'g:i A');
$helper->log('<b>Last Modified On: </b>' . $modifiedDate . ' at ' . $modifiedTime);

// Read the workbook title property
$workbookTitle = $spreadsheet->getProperties()->getTitle();
$helper->log('<b>Title: </b>' . $workbookTitle);

// Read the workbook description property
$description = $spreadsheet->getProperties()->getDescription();
$helper->log('<b>Description: </b>' . $description);

// Read the workbook subject property
$subject = $spreadsheet->getProperties()->getSubject();
$helper->log('<b>Subject: </b>' . $subject);

// Read the workbook keywords property
$keywords = $spreadsheet->getProperties()->getKeywords();
$helper->log('<b>Keywords: </b>' . $keywords);

// Read the workbook category property
$category = $spreadsheet->getProperties()->getCategory();
$helper->log('<b>Category: </b>' . $category);

// Read the workbook company property
$company = $spreadsheet->getProperties()->getCompany();
$helper->log('<b>Company: </b>' . $company);

// Read the workbook manager property
$manager = $spreadsheet->getProperties()->getManager();
$helper->log('<b>Manager: </b>' . $manager);
$s = new PhpOffice\PhpSpreadsheet\Helper\Sample();

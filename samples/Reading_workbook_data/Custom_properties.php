<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/sampleData/example1.xlsx';

// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);
// Load $inputFileName to a PhpSpreadsheet Object
$spreadsheet = $reader->load($inputFileName);

// Read an array list of any custom properties for this document
$customPropertyList = $spreadsheet->getProperties()->getCustomProperties();

// Loop through the list of custom properties
foreach ($customPropertyList as $customPropertyName) {
    $helper->log('<b>' . $customPropertyName . ': </b>');
    // Retrieve the property value
    $propertyValue = $spreadsheet->getProperties()->getCustomPropertyValue($customPropertyName);
    // Retrieve the property type
    $propertyType = $spreadsheet->getProperties()->getCustomPropertyType($customPropertyName);

    // Manipulate properties as appropriate for display purposes
    switch ($propertyType) {
        case 'i':    // integer
            $propertyType = 'integer number';

            break;
        case 'f':    // float
            $propertyType = 'floating point number';

            break;
        case 's':    // string
            $propertyType = 'string';

            break;
        case 'd':    // date
            $propertyValue = is_numeric($propertyValue) ? date('l, d<\s\u\p>S</\s\u\p> F Y g:i A', (int) $propertyValue) : '*****INVALID*****';
            $propertyType = 'date';

            break;
        case 'b':    // boolean
            $propertyValue = ($propertyValue) ? 'TRUE' : 'FALSE';
            $propertyType = 'boolean';

            break;
    }

    $helper->log($propertyValue . ' (' . $propertyType . ')');
}

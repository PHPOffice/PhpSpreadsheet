# PHPExcel User Documentation – Reading Spreadsheet Files


## Security

XML-based formats such as OfficeOpen XML, Excel2003 XML, OASIS and Gnumeric are susceptible to XML External Entity Processing (XXE) injection attacks (for an explanation of XXE injection see http://websec.io/2012/08/27/Preventing-XEE-in-PHP.html) when reading spreadsheet files. This can lead to:

 - Disclosure whether a file is existent
 - Server Side Request Forgery
 - Command Execution (depending on the installed PHP wrappers)
 

To prevent this, PHPExcel sets the LIBXML_DTDLOAD and LIBXML_DTDATTR settings for the XML Readers by default. 


Should you ever need to change these settings, the following method is available through the PHPExcel_Settings:

```
PHPExcel_Settings::setLibXmlLoaderOptions();
```

Allowing you to specify the XML loader settings that you want to use instead.

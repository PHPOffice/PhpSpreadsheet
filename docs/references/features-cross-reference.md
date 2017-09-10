# Features cross reference

- <span style="text-align: center; color: green;">✔</span> Supported
- <span style="text-align: center; color: orange;">●</span> Partially supported
- <span style="text-align: center; color: red;">✖</span> Not supported
- N/A Cannot be supported

<table class="features-cross-reference">
	<tr>
		<th></th>
		<th colspan="7">Readers</th>
		<th colspan="6">Writers</th>
		<th colspan="2">Methods</th>
	</tr>
	<tr>
		<th></th>
		<th>XLS</th>
		<th>XLSX</th>
		<th>Excel2003XML</th>
		<th>Ods</th>
		<th>Gnumeric</th>
		<th>CSV</th>
		<th>SYLK</th>
		<th>XLS</th>
		<th>XLSX</th>
		<th>Ods</th>
		<th>CSV</th>
		<th>HTML</th>
		<th>PDF</th>
		<th>Getters</th>
		<th>Setters</th>
	</tr>
	<tr>
        <td><strong>Reader Options</strong></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Read Data Only (no formatting)</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td>$reader-&gt;getReadDataOnly()</td>
		<td>$reader-&gt;setReadDataOnly()</td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Read Only Specified Worksheets</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td>$reader-&gt;getLoadSheetsOnly()</td>
		<td>$reader-&gt;setLoadSheetsOnly()<br>$reader-&gt;setLoadAllSheets()</td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Read Only Specified Cells</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td>$reader-&gt;getReadFilter()</td>
		<td>$reader-&gt;setReadFilter()</td>
	</tr>
	<tr>
		<td><strong>Document Properties</strong></td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Standard Properties</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Creator</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getCreator()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setCreator()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Creation Date/Time</td>
        <td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getCreated()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setCreated()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Modifier</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getLastModifiedBy()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setLastModifiedBy()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Modified Date/Time</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getModified()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setModified()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Title</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getTitle()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setTitle()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Description</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getDescription()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setDescription()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Subject</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getSubject()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setSubject()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Keywords</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getKeywords()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setKeywords()</td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Extended Properties</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Category</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getCategory()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setCategory()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Company</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getCompany()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setCompany()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Manager</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
        <td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getManager()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setManager()</td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">User-Defined (Custom) Properties</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td>$spreadsheet-&gt;getProperties()-&gt;getCustomProperties()<br>$spreadsheet-&gt;getProperties()-&gt;isCustomPropertySet()<br>$spreadsheet-&gt;getProperties()-&gt;getCustomPropertyValue()<br>$spreadsheet-&gt;getProperties()-&gt;getCustomPropertyType()</td>
		<td>$spreadsheet-&gt;getProperties()-&gt;setCustomProperty()</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Text Properties</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Number Properties</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Date Properties</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Yes/No (Boolean) Properties</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Cell Data Types</strong></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Empty/NULL</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Boolean</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Integer</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Floating Point</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">String</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Error</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Formula</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Array</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Rich Text</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Conditional Formatting</strong></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Rows and Column Properties</strong></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Row Height/Column Width</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Hidden</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Worksheet Properties</strong></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Frozen Panes</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Coloured Tabs</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Cell Formatting</strong></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Number Format Mask</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Alignment</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Horizontal</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Vertical</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Wrapping</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Shring-to-Fit</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Indent</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Background Colour</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Patterned</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Font Attributes</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Font Face</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Font Size</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Bold</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Italic</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Strikethrough</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Underline</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Superscript</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Subscript</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Borders</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Line Style</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Position</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 3em;">Diagonal</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Hyperlinks</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$cell->getHyperlink()->getUrl($url)</td>
		<td>$cell->getHyperlink()->setUrl($url)</td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">http</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Merged Cells</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Cell Comments</strong></td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Rich Text</td>
        <td style="text-align: center; color: red;">✖ <sup>1</sup></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">Alignment</td>
        <td style="text-align: center; color: red;">✖ <sup>2</sup></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td style="text-align: center;">N/A</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
        <td><strong>Cell Validation</strong></td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td style="text-align: center;">N/A</td>
		<td>$cell->getDataValidation()</td>
		<td>$cell->setDataValidation()</td>
	</tr>
	<tr>
		<td><strong>AutoFilters</strong></td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: orange;">●</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>$sheet->getAutoFilter()</td>
		<td>$sheet->setAutoFilter()</td>
	</tr>
	<tr>
		<td style="padding-left: 1em;">AutoFilter Expressions</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: orange;">●</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Custom Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">DateGroup Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Dynamic Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Colour Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Icon Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td style="padding-left: 2em;">Top 10 Filter</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><strong>Macros</strong></td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: green;">✔</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td style="text-align: center; color: red;">✖</td>
		<td>$spreadsheet->getMacrosCode();</td>
		<td>$spreadsheet->setMacrosCode();</td>
	</tr>
	<tr>
		<th></th>
		<th>XLS</th>
		<th>XLSX</th>
		<th>Excel2003XML</th>
		<th>Ods</th>
		<th>Gnumeric</th>
		<th>CSV</th>
		<th>SYLK</th>
		<th>XLS</th>
		<th>XLSX</th>
		<th>Ods</th>
		<th>CSV</th>
		<th>HTML</th>
		<th>PDF</th>
		<th>Getters</th>
		<th>Setters</th>
	</tr>
	<tr>
		<th></th>
		<th colspan="7">Readers</th>
		<th colspan="6">Writers</th>
		<th colspan="2">Methods</th>
	</tr>
</table>

1. Only BIFF8 files support Rich Text. Prior to that, comments could only be plain text
2. Only BIFF8 files support alignment and rotation. Prior to that, comments could only be unformatted text
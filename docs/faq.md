# Frequently asked questions

## There seems to be a problem with character encoding...

It is necessary to use UTF-8 encoding for all texts in PhpSpreadsheet.
If the script uses different encoding then you can convert those texts
with PHP's `iconv()` or `mb_convert_encoding()` functions.

## Fatal error: Allowed memory size of xxx bytes exhausted (tried to allocate yyy bytes) in zzz on line aaa

PhpSpreadsheet holds an "in memory" representation of a spreadsheet, so
it is susceptible to PHP's memory limitations. The memory made available
to PHP can be increased by editing the value of the `memory_limit`
directive in your `php.ini` file, or by using
`ini_set('memory_limit', '128M')` within your code.

Some Readers and Writers are faster than others, and they also use
differing amounts of memory.

## Protection on my worksheet is not working?

When you make use of any of the worksheet protection features (e.g. cell
range protection, prohibiting deleting rows, ...), make sure you enable
worksheet security. This can for example be done like this:

```php
$spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
```

## Feature X is not working with Reader\_Y / Writer\_Z

Not all features of PhpSpreadsheet are implemented in all of the Reader
/ Writer classes. This is mostly due to underlying libraries not
supporting a specific feature or not having implemented a specific
feature.

For example autofilter is not implemented in PEAR
Spreadsheet\_Excel\_writer, which is the base of our Xls writer.

We are slowly building up a list of features, together with the
different readers and writers that support them, in the [features cross
reference](./references/features-cross-reference.md).

## Formulas don't seem to be calculated in Excel2003 using compatibility pack?

This is normal behaviour of the compatibility pack, `Xlsx` displays this
correctly. Use `\PhpOffice\PhpSpreadsheet\Writer\Xls` if you really need
calculated values, or force recalculation in Excel2003.

## Setting column width is not 100% accurate

Trying to set column width, I experience one problem. When I open the
file in Excel, the actual width is 0.71 less than it should be.

The short answer is that PhpSpreadsheet uses a measure where padding is
included. See [how to set a column's width](./topics/recipes.md#setting-a-columns-width)
for more details.

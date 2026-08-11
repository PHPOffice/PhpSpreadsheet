# Configuration Settings

Once you have included the PhpSpreadsheet files in your script, but
before instantiating a `Spreadsheet` object or loading a workbook file,
there are a number of configuration options that can be set which will
affect the subsequent behaviour of the script.

## Cell collection caching

By default, PhpSpreadsheet holds all cell objects in memory, but
you can specify alternatives to reduce memory consumption at the cost of speed.
Read more about [memory saving](./memory_saving.md).

To enable cell caching, you must provide your own implementation of cache like so:

```php
$cache = new MyCustomPsr16Implementation();

\PhpOffice\PhpSpreadsheet\Settings::setCache($cache);
```

## Language/Locale

Some localisation elements have been included in PhpSpreadsheet. You can
set a locale by changing the settings. To set the locale to Brazilian
Portuguese you would use:

```php
$locale = 'pt_br';
$validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale);
if (!$validLocale) {
    echo 'Unable to set locale to ' . $locale . " - reverting to en_us" . PHP_EOL;
}
```

- If Brazilian Portuguese language files aren't available, then Portuguese
will be enabled instead
- If Portuguese language files also aren't available,
then the `setLocale` method will return `false`, and American English
(en\_us) settings will be used throughout.

More details of the features available once a locale has been set,
including a list of the languages and locales currently supported, can
be found in [Locale Settings for
Formulas](./recipes.md#locale-settings-for-formulas).

Additional localization elements (currency code, thousands separator, and decimal separator) are available in `PhpOffice\PhpSpreadsheet\Shared\StringHelper`:

```php
StringHelper::setCurrencyCode('€');
StringHelper::setThousandsSeparator('.');
StringHelper::setDecimalSeparator(',');
```

You can use the Php function `setLocale` to try to set these values
without knowing beforehand what symbols are needed.
```php
StringHelper::setCurrencyCode(null);
StringHelper::setThousandsSeparator(null);
StringHelper::setDecimalSeparator(null);
$result = setLocale(LC_ALL, 'pt_br.UTF-8');
```
However, this function maintains its information at the process level, not the thread level,
and its use is therefore discouraged.

A less-troublesome replacement is available starting with PhpSpreadsheet 5.4.
```php
$result = StringHelper::setLocale('pt_br'); // will restore defaults if argument is null
```
This will set the locale and the 3 StringHelper values all at once.
It requires the `Intl` extension, which is not a requirement for PhpSpreadsheet as a whole.
For that reason, it returns a boolean result, which will be `false`
if `Intl` is not available, or if it does not consider the supplied locale to be valid.

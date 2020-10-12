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
- If Portuguese language files aren't available,
then the `setLocale()` method will return an error, and American English
(en\_us) settings will be used throughout.

More details of the features available once a locale has been set,
including a list of the languages and locales currently supported, can
be found in [Locale Settings for
Formulae](./recipes.md#locale-settings-for-formulae).

## HTTP client

In order to use the `WEBSERVICE` function in formulae, you must configure an
HTTP client. Assuming you chose Guzzle 7, this can be done like:


```php
use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use PhpOffice\PhpSpreadsheet\Settings;

$client = new Client();
$requestFactory = new RequestFactory();

Settings::setHttpClient($client, $requestFactory);
``` 

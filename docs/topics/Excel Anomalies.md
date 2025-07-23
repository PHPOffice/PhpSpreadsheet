# Excel Anomalies

This is documentation for some behavior in Excel itself which we
just do not understand, or which may come as a surprise to the user.

## Date Number Format

My system short date format is set to `yyyy-mm-dd`. Excel, for a very long time, did not include that amongst its formatting choices for dates, so it needed to be added as a custom format - no big deal. It has recently been added to the list of date formats, but ...

I used Excel to create a spreadsheet, and included some dates, specifying `yyyy-mm-dd` formatting. When I looked at the resulting spreadsheet, I was surprised to see that Excel had stored the style not as `yyyy-mm-dd`, but rather as builtin style 14 (system short date format). Apparently the fact that the Excel styling matched my system choice was sufficient for it to override my choice! This is an astonishingly user-hostile implementation. Even though there are formats which, by design, "respond to changes in regional date and time settings", and even though the format I selected was not among those, Excel decided it was appropriate to vary the display even when I said I wanted an unvarying format. I assume, but have not confirmed, that this applies to formats other than `yyyy-mm-dd`.

Note that this is not a problem when using PhpSpreadsheet to set the style, only when you let Excel do it. And, in that case, after a little experimentation, I figured out a format that Excel doesn't sabotage `[Black]yyyy-mm-dd`.

If you have a spreadsheet that has been altered in this way, it can be fixed with the following PhpSpreadsheet code:
```php
        foreach ($spreadsheet->getCellXfCollection() as $style) {
            $numberFormat = $style->getNumberFormat();
            // okay to use NumberFormat::SHORT_DATE_INDEX below
            if ($numberFormat->getBuiltInFormatCode() === 14) {
                $numberFormat->setFormatCode('yyyy-mm-dd');
            }
        }
```
Starting with PhpSpreadsheet 4.5.0, this can be simplified to:
```php
        $spreadsheet->replaceBuiltinNumberFormat(
            \PhpOffice\PhpSpreadsheet\Style\NumberFormat::SHORT_DATE_INDEX,
            'yyyy-mm-dd'
        );
```

## Negative Time Intervals

You have a time in one cell, and a time in another, and you want to subtract and display the result in `h:mm` format. No problem if the result is positive. But, if it's negative, Excel just fills the cell with `#`. There is a solution of sorts. If you use a 1904 base date (default on Mac), the negative interval will work just fine. Alas, no dice if you use a 1900 base data (default on Windows). No idea why they can't fix that - the existing implementation can't really be something that anybody actually wants. Note that it is *not* safe to change the base date for an existing spreadsheet, so, if this is something you want to do, make sure you change the base date before populating any data.

## Long-ago Dates

Excel does not support dates before either 1900-01-01 (Windows default) or 1904-01-01 (Mac default). For the 1900 base year, there is the additional problem that non-existent date 1900-02-29 is squeezed between 1900-02-28 and 1900-03-01.

## Weird Fractions

Similar fraction formats have inconsistent results in Excel. For example, if a cell contains the value 1 and the cell's format is `0 0/0`, it will display as `1 0/1`. But, if the cell's format is `? ??/???`, it will display as `1`. See [this issue](https://github.com/PHPOffice/PhpSpreadsheet/issues/3625), which remains open because, in the absence of usable documentation, we aren't sure how to handle things.

## COUNTIF and Text Cells

In Excel, COUNTIF appears to ignore text cells, behavior which doesn't seem to be documented anywhere. See [this issue](https://github.com/PHPOffice/PhpSpreadsheet/issues/3802), which remains open because, in the absence of usable documentation, we aren't sure how to handle things.
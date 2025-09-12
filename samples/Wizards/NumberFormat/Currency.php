<?php

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\CurrencyNegative;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}

$negatives = [
    CurrencyNegative::minus,
    CurrencyNegative::redMinus,
    CurrencyNegative::parentheses,
    CurrencyNegative::redParentheses,
];
$negativesString = [
    'CurrencyNegative::minus',
    'CurrencyNegative::redMinus',
    'CurrencyNegative::parentheses',
    'CurrencyNegative::redParentheses',
];

$currencies = [
    '$' => 'US Dollars ($)',
    '€' => 'Euro (€)',
    '¥' => 'Japanese Yen (¥)',
    '£' => 'Pound Sterling (£)',
    '₹' => 'Rupee (₹)',
    '₽' => 'Rouble (₽)',
];

?>
    <form action="Currency.php" method="POST">
        <div class="mb-3 row">
            <label for="number" class="col-sm-2 col-form-label">Sample Number Value</label>
            <div class="col-sm-10">
                <input name="number" type="text" size="8" value="<?php echo StringHelper::convertPostToString('number', '1234.5678'); ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <hr />
        </div>
        <div class="mb-3 row">
            <label for="currency" class="col-sm-2 col-form-label">Currency</label>
            <div class="col-sm-10">
                <select name="currency" class="form-select">
                    <?php foreach ($currencies as $currencySymbol => $currencyName) {
                        echo "<option value=\"{$currencySymbol}\" " . ((isset($_POST['currency']) && $_POST['currency'] === $currencySymbol) ? 'selected' : '') . ">{$currencyName}</option>", PHP_EOL;
                    } ?>
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="decimals" class="col-sm-2 col-form-label">Decimal Places</label>
            <div class="col-sm-10">
                <input name="decimals" type="number" size="2" min="0" max="14" value="<?php echo StringHelper::convertPostToString('decimals', '2'); ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="thousands" class="col-sm-2 col-form-label">Use Thousands Separator</label>
            <div class="col-sm-10">
                <input name="thousands" type="checkbox" <?php echo (isset($_POST['thousands'])) ? 'value="on"' : ''; ?> <?php echo (isset($_POST['thousands'])) ? 'checked' : ''; ?>>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="position" class="col-sm-2 col-form-label">Currency Position</label>
            <div class="col-sm-10">
                <input name="position" type="radio" value="1" <?php echo ((isset($_POST['position']) === false) || ($_POST['position'] === '1')) ? 'checked' : ''; ?>>Leading
                <input name="position" type="radio" value="0" <?php echo (isset($_POST['position']) && $_POST['position'] === '0') ? 'checked' : ''; ?>>Trailing
            </div>
        </div>
        <div class="mb-3 row">
            <label for="negative" class="col-sm-2 col-form-label">Negative Numbers</label>
            <div class="col-sm-10">
                <input name="negative" type="radio" value="0"  <?php echo (!isset($_POST['negative']) || $_POST['negative'] === '0') ? 'checked' : ''; ?>>Minus Sign
                <input name="negative" type="radio" value="1"  <?php echo (isset($_POST['negative']) && $_POST['negative'] === '1') ? 'checked' : ''; ?>>Red Minus Sign
                <input name="negative" type="radio" value="2"  <?php echo (isset($_POST['negative']) && $_POST['negative'] === '2') ? 'checked' : ''; ?>>Parentheses
                <input name="negative" type="radio" value="3"  <?php echo (isset($_POST['negative']) && $_POST['negative'] === '3') ? 'checked' : ''; ?>>Red Parentheses
            </div>
        </div>
        <div class="mb-3 row">
            <div class="col-sm-10">
                <input  class="btn btn-primary" name="submit" type="submit" value="Display Mask"><br />
            </div>
        </div>
    </form>

<?php
/**     If the user has submitted the form, then we need to use the wizard to build a mask and display the result */
if (isset($_POST['submit'])) {
    if (!is_numeric($_POST['number'])) {
        $helper->log('The Sample Number Value must be numeric');
    } elseif (!is_numeric($_POST['decimals']) || str_contains((string) $_POST['decimals'], '.') || (int) $_POST['decimals'] < 0) {
        $helper->log('The Decimal Places value must be positive integer');
    } elseif (!in_array($_POST['currency'], array_keys($currencies), true)) {
        $helper->log('Unrecognized currency symbol');
    } else {
        try {
            $negative = $negatives[$_POST['negative']] ?? CurrencyNegative::minus; //* @phpstan-ignore-line
            $wizard = new Wizard\Currency($_POST['currency'], (int) $_POST['decimals'], isset($_POST['thousands']), (bool) $_POST['position']);
            $wizard->setNegative($negative);
            $mask = $wizard->format();
            $example = (string) NumberFormat::toFormattedString((float) $_POST['number'], $mask, [HtmlWriter::class, 'formatColorStatic']);
            $helper->log('<hr /><b>Code:</b><br />');
            $helper->log('use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;');
            $helper->log('use PhpOffice\PhpSpreadsheet\Style\NumberFormat\CurrencyNegative;');
            $helper->log(
                "\$wizard = new  Wizard\\Currency('{$_POST['currency']}', {$_POST['decimals']}, Wizard\\Number::"
                . (isset($_POST['thousands']) ? 'WITH_THOUSANDS_SEPARATOR' : 'WITHOUT_THOUSANDS_SEPARATOR')
                . ', Wizard\Currency::' . (((bool) $_POST['position']) ? 'LEADING_SYMBOL' : 'TRAILING_SYMBOL')
                . ');'
            );
            $helper->log('$wizard->setNegative(' . $negativesString[$_POST['negative']] . ');'); //* @phpstan-ignore-line
            $helper->log('$mask = $wizard->format();');
            $helper->log('<br />echo (string) $mask;');
            $helper->log('<hr /><b>Mask:</b><br />');
            $helper->log($mask . '<br />');
            $helper->log('<br /><b>Example:</b><br />');
            $helper->log($example);
        } catch (SpreadsheetException $e) {
            $helper->log("Exception: {$e->getMessage()}");
        }
    }
}

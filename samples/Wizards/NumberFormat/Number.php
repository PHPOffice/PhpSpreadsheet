<?php

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

require __DIR__ . '/../../Header.php';

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
?>
    <form action="Number.php" method="POST">
        <div class="mb-3 row">
            <label for="number" class="col-sm-2 col-form-label">Sample Number Value</label>
            <div class="col-sm-10">
                <input name="number" type="text" size="8" value="<?php echo (isset($_POST['number'])) ? htmlentities($_POST['number'], Settings::htmlEntityFlags()) : '1234.5678'; ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <hr />
        </div>
        <div class="mb-3 row">
            <label for="decimals" class="col-sm-2 col-form-label">Decimal Places</label>
            <div class="col-sm-10">
                <input name="decimals" type="number" size="2" min="0" max="14" value="<?php echo (isset($_POST['decimals'])) ? htmlentities($_POST['decimals'], Settings::htmlEntityFlags()) : '2'; ?>">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="thousands" class="col-sm-2 col-form-label">Use Thousands Separator</label>
            <div class="col-sm-10">
                <input name="thousands" type="checkbox" <?php echo (isset($_POST['thousands'])) ? 'value="on"' : ''; ?> <?php echo (isset($_POST['thousands'])) ? 'checked' : ''; ?>>
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
    } elseif (!is_numeric($_POST['decimals']) || strpos($_POST['decimals'], '.') !== false || (int) $_POST['decimals'] < 0) {
        $helper->log('The Decimal Places value must be positive integer');
    } else {
        try {
            $wizard = new Wizard\Number($_POST['decimals'], isset($_POST['thousands']));
            $mask = $wizard->format();
            $example = NumberFormat::toFormattedString((float) $_POST['number'], $mask);
            $helper->log('<hr /><b>Code:</b><br />');
            $helper->log('use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;');
            $helper->log(
                "\$mask = Wizard\\Number({$_POST['decimals']}, Wizard\\Number::" .
                (isset($_POST['thousands']) ? 'WITH_THOUSANDS_SEPARATOR' : 'WITHOUT_THOUSANDS_SEPARATOR') .
                ');<br />'
            );
            $helper->log('echo (string) $mask;');
            $helper->log('<hr /><b>Mask:</b>');
            $helper->log($mask);
            $helper->log('<b>Example:</b>');
            $helper->log($example);
        } catch (SpreadsheetException $e) {
            $helper->log("Exception: {$e->getMessage()}");
        }
    }
}

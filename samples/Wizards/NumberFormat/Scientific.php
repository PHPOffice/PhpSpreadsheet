<?php

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard;

require __DIR__ . '/../../Header.php';

?>
    <form action="Scientific.php" method="POST">
        <table border="0" cellpadding="6" cellspacing="6">
            <tr>
                <td>
                    <b>Sample Number Value</b>
                </td>
                <td>
                    <input name="number" type="text" size="8" value="<?php echo (isset($_POST['number'])) ? htmlentities($_POST['number'], Settings::htmlEntityFlags()) : ''; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr />
                </td>
            </tr>
            <tr>
                <td>
                    <b>Decimal Places</b>
                </td>
                <td>
                    <input name="decimals" type="text" size="2" value="<?php echo (isset($_POST['decimals'])) ? htmlentities($_POST['decimals'], Settings::htmlEntityFlags()) : ''; ?>">
                </td>
            </tr>
        </table>
        <input name="submit" type="submit" value="calculate"><br />
    </form>

<?php
/**     If the user has submitted the form, then we need to use the wizard to build a mask and display the result */
if (isset($_POST['submit'])) {
    if (!is_numeric($_POST['number'])) {
        $helper->log('The Sample Number Value must be numeric');
    } elseif (!is_numeric($_POST['decimals']) || strpos($_POST['decimals'], '.') !== false || (int) $_POST['decimals'] < 0) {
        $helper->log('The Decimal Places value must be positive integer');
    } else {
        $wizard = new Wizard\Scientific($_POST['decimals']);
        $mask = $wizard->format();
        $example = (string) NumberFormat::toFormattedString((float) $_POST['number'], $mask);
        $helper->log('<hr /><b>Mask:</b><br />');
        $helper->log($mask . '<br />');
        $helper->log('<br /><b>Example:</b><br />');
        $helper->log($example);
    }
}

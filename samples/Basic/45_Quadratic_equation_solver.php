<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
?>
<form action="45_Quadratic_equation_solver.php" method="POST">
    Enter the coefficients for the Ax<sup>2</sup> + Bx + C = 0
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <b>A&nbsp;</b>
            </td>
            <td>
                <input name="A" type="text" size="8" value="<?php echo (isset($_POST['A'])) ? htmlentities($_POST['A'], Settings::htmlEntityFlags()) : ''; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <b>B&nbsp;</b>
            </td>
            <td>
                <input name="B" type="text" size="8" value="<?php echo (isset($_POST['B'])) ? htmlentities($_POST['B'], Settings::htmlEntityFlags()) : ''; ?>">
            </td>
        </tr>
        <tr>
            <td><b>C&nbsp;</b>
            </td>
            <td>
                <input name="C" type="text" size="8" value="<?php echo (isset($_POST['C'])) ? htmlentities($_POST['C'], Settings::htmlEntityFlags()) : ''; ?>">
            </td>
        </tr>
    </table>
    <input name="submit" type="submit" value="calculate"><br />
    If A=0, the equation is not quadratic.
</form>

<?php
/**     If the user has submitted the form, then we need to execute a calculation * */
if (isset($_POST['submit'])) {
    if ($_POST['A'] == 0) {
        $helper->log('The equation is not quadratic');
    } else {
        // Calculate and Display the results
        $helper->log('<hr /><b>Roots:</b><br />');

        $discriminantFormula = '=POWER(' . $_POST['B'] . ',2) - (4 * ' . $_POST['A'] . ' * ' . $_POST['C'] . ')';
        $discriminant = Calculation::getInstance()->calculateFormula($discriminantFormula);

        $r1Formula = '=IMDIV(IMSUM(-' . $_POST['B'] . ',IMSQRT(' . $discriminant . ')),2 * ' . $_POST['A'] . ')';
        $r2Formula = '=IF(' . $discriminant . '=0,"Only one root",IMDIV(IMSUB(-' . $_POST['B'] . ',IMSQRT(' . $discriminant . ')),2 * ' . $_POST['A'] . '))';

        $helper->log(Calculation::getInstance()->calculateFormula($r1Formula));
        $helper->log(Calculation::getInstance()->calculateFormula($r2Formula));
        $callEndTime = microtime(true);
        $helper->logEndingNotes();
    }
}

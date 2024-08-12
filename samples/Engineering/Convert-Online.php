<?php

use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Settings;

require __DIR__ . '/../Header.php';

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}

$categories = ConvertUOM::getConversionCategories();
$defaultCategory = $_POST['category'] ?? $categories[0];
$units = [];
foreach ($categories as $category) {
    $categoryUnits = ConvertUOM::getConversionCategoryUnitDetails($category)[$category];
    $categoryUnits = array_unique(
        array_combine(
            array_column($categoryUnits, 'unit'),
            array_column($categoryUnits, 'description')
        )
    );
    $units[$category] = $categoryUnits;
}

?>
<form action=Convert-Online.php method="POST">
    <div class="mb-3 row">
        <label for="category" class="col-sm-2 col-form-label">Category</label>
        <div class="col-sm-10">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <?php foreach ($categories as $category) {
                    echo "<option value=\"{$category}\" " . ((isset($_POST['category']) && $_POST['category'] === $category) ? 'selected' : '') . ">{$category}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
        <div class="col-sm-10">
            <input name="quantity" type="text" size="8" value="<?php echo (isset($_POST['quantity'])) ? htmlentities($_POST['quantity'], Settings::htmlEntityFlags()) : '1.0'; ?>">
        </div>
    </div>
    <div class="mb-3 row">
        <label for="fromUnit" class="col-sm-2 col-form-label">From Unit</label>
        <div class="col-sm-10">
            <select name="fromUnit" class="form-select">
                <?php foreach ($units[$defaultCategory] as $fromUnitCode => $fromUnitName) {
                    echo "<option value=\"{$fromUnitCode}\" " . ((isset($_POST['fromUnit']) && $_POST['fromUnit'] === $fromUnitCode) ? 'selected' : '') . ">{$fromUnitName}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <label for="toUnit" class="col-sm-2 col-form-label">To Unit</label>
        <div class="col-sm-10">
            <select name="toUnit" class="form-select">
                <?php foreach ($units[$defaultCategory] as $toUnitCode => $toUnitName) {
                    echo "<option value=\"{$toUnitCode}\" " . ((isset($_POST['toUnit']) && $_POST['toUnit'] === $toUnitCode) ? 'selected' : '') . ">{$toUnitName}</option>", PHP_EOL;
                } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-10">
            <input  class="btn btn-primary" name="submitx" type="submit" value="Convert"><br />
        </div>
    </div>
</form>

<?php
/**     If the user has submitted the form, then we need to calculate the value and display the result */
if (isset($_POST['quantity'], $_POST['fromUnit'], $_POST['toUnit'])) {
    $quantity = $_POST['quantity'];
    $fromUnit = $_POST['fromUnit'];
    $toUnit = $_POST['toUnit'];
    if (isset($units[$_POST['category']][$fromUnit], $units[$_POST['category']][$toUnit])) {
        /** @var float|string */
        $result = ConvertUOM::CONVERT($quantity, $fromUnit, $toUnit);

        echo "{$quantity} {$units[$_POST['category']][$fromUnit]} is {$result} {$units[$_POST['category']][$toUnit]}", PHP_EOL;
    } else {
        echo 'Please enter quantity and select From Unit and To Unit', PHP_EOL;
    }
} else {
    echo 'Please enter quantity and select From Unit and To Unit', PHP_EOL;
}

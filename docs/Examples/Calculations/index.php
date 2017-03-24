<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PhpSpreadsheet Calculation Function Examples</title>

</head>
<body>

<?php

echo '<h1>PhpSpreadsheet Calculation Function Examples</h1>';

$exampleTypeList = glob('./*', GLOB_ONLYDIR);

foreach ($exampleTypeList as $exampleType) {
    echo '<h2>' . pathinfo($exampleType, PATHINFO_BASENAME) . ' Function Examples</h2>';

    $exampleList = glob('./' . $exampleType . '/*.php');

    foreach ($exampleList as $exampleFile) {
        $fileData = file_get_contents($exampleFile);

        $h1Pattern = '#<h1>(.*?)</h1>#';
        $h2Pattern = '#<h2>(.*?)</h2>#';

        if (preg_match($h1Pattern, $fileData, $out)) {
            $h1Text = $out[1];
            $h2Text = (preg_match($h2Pattern, $fileData, $out)) ? $out[1] : '';

            echo '<a href="',$exampleFile,'">',$h1Text,'</a><br />';
            if ($h2Text > '') {
                echo $h2Text,'<br />';
            }
        }
    }
}

?>
<body>
</html>
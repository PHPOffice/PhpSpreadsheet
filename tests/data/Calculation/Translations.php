<?php

return [
    [
        '=JOURS360(DATE(2010;2;5);DATE(2010;12;31);VRAI)',
        'fr',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=DAGEN360(DATUM(2010;2;5);DATUM(2010;12;31);WAAR)',
        'nl',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=DIAS360(DATA(2010;2;5);DATA(2010;12;31);VERDADEIRO)',
        'pt_br',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
    [
        '=ДНЕЙ360(ДАТА(2010;2;5);ДАТА(2010;12;31);ИСТИНА)',
        'ru',
        '=DAYS360(DATE(2010,2,5),DATE(2010,12,31),TRUE)',
    ],
];

<?php

return [
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
    [
        '=TEKST.SAMENVOEGEN(A1; " "; B1)',
        'nl',
        '=CONCATENATE(A1, " ", B1)',
    ],
    [
        '=TEKST.SAMENVOEGEN("""Hello "; B1; ""","; " I said.")',
        'nl',
        '=CONCATENATE("""Hello ", B1, """,", " I said.")',
    ],
    [
        '=ЕСЛИ(1;1;1)',
        'ru',
        '=IF(1,1,1)',
    ],
    [
        '=ИСКЛИЛИ(1;1)',
        'ru',
        '=XOR(1,1)',
    ],
    [
        '=TEKST.SAMENVOEGEN(JAAR(VANDAAG());
            " is ";
            ALS(
                DAGEN(DATUM(JAAR(VANDAAG())+1; 1; 1); DATUM(JAAR(VANDAAG()); 1; 1)) = 365;
                "NOT a Leap Year";
                "a Leap Year"
            )
        )',
        'nl',
        '=CONCATENATE(YEAR(TODAY()),
            " is ",
            IF(
                DAYS(DATE(YEAR(TODAY())+1, 1, 1), DATE(YEAR(TODAY()), 1, 1)) = 365,
                "NOT a Leap Year",
                "a Leap Year"
            )
        )',
    ],
    'Spanish with accented character' => [
        '=AÑO(B1)',
        'es',
        '=YEAR(B1)',
    ],
    'Bulgarian with accent and period' => [
        '=ПОЛУЧИТЬ.ДАННЫЕ.СВОДНОЙ.ТАБЛИЦЫ(B1)',
        'bg',
        '=GETPIVOTDATA(B1)',
    ],
    'Czech with accent and period' => [
        '=DSMODCH.VÝBĚR(B1)',
        'cs',
        '=DSTDEV(B1)',
    ],
    'Turkish with accent and period' => [
        '=İŞGÜNÜ.ULUSL(B1)',
        'tr',
        '=WORKDAY.INTL(B1)',
    ],
    [
        '=STØRST(ABS({2,-3;-4,5}); ABS{-2,3;4,-5})',
        'nb',
        '=MAX(ABS({2,-3;-4,5}), ABS{-2,3;4,-5})',
    ],
    'not fooled by *RC' => [
        '=3*RC(B1)',
        'fr',
        '=3*RC(B1)',
    ],
    'handle * for ROW' => [
        '=3*LIGNE(B1)',
        'fr',
        '=3*ROW(B1)',
    ],
];

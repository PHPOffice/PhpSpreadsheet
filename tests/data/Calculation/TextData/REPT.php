<?php

return [
    ['#VALUE!'], // exception not enough args
    ['#VALUE!', '"ABC"'], // exception not enough args
    ['#VALUE!', '"ABC"', '"DEF"'],
    ['ABCABCABC', '"ABC"', 3],
    ['ABCABC', '"ABC"', 2.2],
    ['', '"ABC"', 0],
    ['TRUETRUE', true, 2],
    ['111', 1, 3],
    ['δύο δύο ', '"δύο "', 2],
    ['#VALUE!', '"ABC"', -1],
];

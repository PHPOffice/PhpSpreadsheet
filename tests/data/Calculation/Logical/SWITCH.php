<?php

return [
    // Must be C
    [
        "C",
        "A",
        "A",
        "C",
        "B",
        "D",
        "??"
    ],
    // Must be Female
    [
        "Female",
        2,
        "1",
        "Male",
        "2",
        "Female"
    ],
    // Must be X using default
    [
        "X",
        "U",
        "ABC",
        "Y",
        "DEF",
        "Z",
        "X"
    ],
    // Must be N/A default value not defined
    [
        "#N/A",
        "U",
        "ABC",
        "Y",
        "DEF",
        "Z"
    ],
    // Must be value - no parameter
    [
        "#VALUE!"
    ],
];

<?php

// Excel DateTimeStamp	Timezone				Result		    Comments
return [
	[ 22269,			'America/New_York',		-285102000	],	//	19-Dec-1960 00:00:00 EST => 19-Dec-1960 05:00:00 UTC
	[ 25569,			'America/New_York',		18000		],	//	01-Jan-1970 00:00:00 EST => 01-Jan-1970 05:00:00 UTC    PHP Base Date
	[ 30292,			'America/New_York',		408085200	],	//	07-Dec-1982 00:00:00 EST => 07-Dec-1982 05:00:00 UTC
	[ 39611,			'America/New_York',		1213243200	],	//	12-Jun-2008 00:00:00 EDT => 12-Jun-2008 04:00:00 UTC
	[ 50424,			'America/New_York',		2147490000	],	//	19-Jan-2038 00:00:00 EST => 19-Jan-2038 05:00:00 UTC    PHP 32-bit Latest Date
	[ 22345.56789,		'America/New_York',		-278486534	],	//	05-Mar-1961 13:37:46 EST => 05-Mar-1961 18:37:46 UTC
	[ 22345.6789,		'America/New_York',		-278476943	],	//	05-Mar-1961 16:17:37 EST => 05-Mar-1961 21:17:37 UTC
	[ 0.5,				'America/New_York',		61200 		],	//	12:00:00 EST => 17:00:00 UTC
	[ 0.75,				'America/New_York',		82800		],	//	18:00.00 EST => 23:00:00 UTC
	[ 0.12345,			'America/New_York',		28666		],	//	02:57:46 EST => 07:57:46 UTC
	[ 41215,			'America/New_York',		1351828800	],	//	02-Nov-2012 00:00:00 EDT => 02-Nov-2012 04:00:00 UTC
	[ 22269,			'Pacific/Auckland',		-285163200	],	//	19-Dec-1960 00:00:00 NZST => 18-Dec-1960 12:00:00 UTC
	[ 25569,			'Pacific/Auckland',		-43200		],	//	01-Jan-1970 00:00:00 NZST => 31-Dec-1969 12:00:00 UTC    PHP Base Date
	[ 30292,			'Pacific/Auckland',		408020400	],	//	07-Dec-1982 00:00:00 NZDT => 06-Dec-1982 11:00:00 UTC
	[ 39611,			'Pacific/Auckland',		1213185600	],	//	12-Jun-2008 00:00:00 NZST => 11-Jun-2008 12:00:00 UTC
	[ 50423.5,			'Pacific/Auckland',		2147382000	],	//	18-Jan-2038 12:00:00 NZDT => 17-Jan-2038 23:00:00 UTC    PHP 32-bit Latest Date
	[ 22345.56789,		'Pacific/Auckland',		-278547734	],	//	05-Mar-1961 13:37:46 NZST => 05-Mar-1961 01:37:46 UTC
	[ 22345.6789,		'Pacific/Auckland',		-278538143	],	//	05-Mar-1961 16:17:37 NZST => 05-Mar-1961 04:17:37 UTC
	[ 0.5,				'Pacific/Auckland',		0           ],	//	12:00:00 NZST => 00:00:00 UTC
	[ 0.75,				'Pacific/Auckland',		21600		],	//	18:00.00 NZST => 06:00:00 UTC
	[ 0.12345,			'Pacific/Auckland',		-32534		],	//	02:57:46 NZST => 14:57:46 UTC
	[ 41215,			'Pacific/Auckland',		1351767600	],	//	02-Nov-2012 00:00:00 NZDT => 01-Nov-2012 11:00:00 UTC
];

<?php
declare(strict_types = 1);

/*Configure::write('App.paths.locales', array_merge(
    Configure::read('App.paths.locales'),
    [Plugin::path('CkTools') . 'src/Locale/']
));*/

return [
    'eu_countries' => [
        'be',
        'bg',
        'cz',
        'dk',
        'de',
        'ee',
        'ie',
        'es',
        'fr',
        'it',
        'cy',
        'lv',
        'lt',
        'lu',
        'hu',
        'mt',
        'nl',
        'at',
        'pl',
        'pt',
        'ro',
        'si',
        'sk',
        'fi',
        'se',
    ],
    'countries' => [
        'af' => __d('countries', 'af'),
        'eg' => __d('countries', 'eg'),
        'ax' => __d('countries', 'ax'),
        'al' => __d('countries', 'al'),
        'dz' => __d('countries', 'dz'),
        'as' => __d('countries', 'as'),
        'vi' => __d('countries', 'vi'),
        'ad' => __d('countries', 'ad'),
        'ao' => __d('countries', 'ao'),
        'ai' => __d('countries', 'ai'),
        'aq' => __d('countries', 'aq'),
        'ag' => __d('countries', 'ag'),
        'gq' => __d('countries', 'gq'),
        'ar' => __d('countries', 'ar'),
        'am' => __d('countries', 'am'),
        'aw' => __d('countries', 'aw'),
        'ac' => __d('countries', 'ac'),
        'az' => __d('countries', 'az'),
        'et' => __d('countries', 'et'),
        'au' => __d('countries', 'au'),
        'bs' => __d('countries', 'bs'),
        'bh' => __d('countries', 'bh'),
        'bd' => __d('countries', 'bd'),
        'bb' => __d('countries', 'bb'),
        'be' => __d('countries', 'be'),
        'bz' => __d('countries', 'bz'),
        'bj' => __d('countries', 'bj'),
        'bm' => __d('countries', 'bm'),
        'bt' => __d('countries', 'bt'),
        'bo' => __d('countries', 'bo'),
        'ba' => __d('countries', 'ba'),
        'bw' => __d('countries', 'bw'),
        'bv' => __d('countries', 'bv'),
        'br' => __d('countries', 'br'),
        'vg' => __d('countries', 'vg'),
        'io' => __d('countries', 'io'),
        'bn' => __d('countries', 'bn'),
        'bg' => __d('countries', 'bg'),
        'bf' => __d('countries', 'bf'),
        'bi' => __d('countries', 'bi'),
        'cl' => __d('countries', 'cl'),
        'cn' => __d('countries', 'cn'),
        'ck' => __d('countries', 'ck'),
        'cr' => __d('countries', 'cr'),
        'ci' => __d('countries', 'ci'),
        'dk' => __d('countries', 'dk'),
        'de' => __d('countries', 'de'),
        'sh' => __d('countries', 'sh'),
        'dg' => __d('countries', 'dg'),
        'dm' => __d('countries', 'dm'),
        'do' => __d('countries', 'do'),
        'dj' => __d('countries', 'dj'),
        'ec' => __d('countries', 'ec'),
        'sv' => __d('countries', 'sv'),
        'er' => __d('countries', 'er'),
        'ee' => __d('countries', 'ee'),
        'fk' => __d('countries', 'fk'),
        'fo' => __d('countries', 'fo'),
        'fj' => __d('countries', 'fj'),
        'fi' => __d('countries', 'fi'),
        'fr' => __d('countries', 'fr'),
        'gf' => __d('countries', 'gf'),
        'pf' => __d('countries', 'pf'),
        'tf' => __d('countries', 'tf'),
        'ga' => __d('countries', 'ga'),
        'gm' => __d('countries', 'gm'),
        'ge' => __d('countries', 'ge'),
        'gh' => __d('countries', 'gh'),
        'gi' => __d('countries', 'gi'),
        'gd' => __d('countries', 'gd'),
        'gr' => __d('countries', 'gr'),
        'gl' => __d('countries', 'gl'),
        'gp' => __d('countries', 'gp'),
        'gu' => __d('countries', 'gu'),
        'gt' => __d('countries', 'gt'),
        'gg' => __d('countries', 'gg'),
        'gn' => __d('countries', 'gn'),
        'gw' => __d('countries', 'gw'),
        'gy' => __d('countries', 'gy'),
        'ht' => __d('countries', 'ht'),
        'hm' => __d('countries', 'hm'),
        'hn' => __d('countries', 'hn'),
        'hk' => __d('countries', 'hk'),
        'in' => __d('countries', 'in'),
        'id' => __d('countries', 'id'),
        'im' => __d('countries', 'im'),
        'iq' => __d('countries', 'iq'),
        'ir' => __d('countries', 'ir'),
        'ie' => __d('countries', 'ie'),
        'is' => __d('countries', 'is'),
        'il' => __d('countries', 'il'),
        'it' => __d('countries', 'it'),
        'jm' => __d('countries', 'jm'),
        'jp' => __d('countries', 'jp'),
        'ye' => __d('countries', 'ye'),
        'je' => __d('countries', 'je'),
        'jo' => __d('countries', 'jo'),
        'ky' => __d('countries', 'ky'),
        'kh' => __d('countries', 'kh'),
        'cm' => __d('countries', 'cm'),
        'ca' => __d('countries', 'ca'),
        'ic' => __d('countries', 'ic'),
        'cv' => __d('countries', 'cv'),
        'kz' => __d('countries', 'kz'),
        'qa' => __d('countries', 'qa'),
        'ke' => __d('countries', 'ke'),
        'kg' => __d('countries', 'kg'),
        'ki' => __d('countries', 'ki'),
        'cc' => __d('countries', 'cc'),
        'co' => __d('countries', 'co'),
        'km' => __d('countries', 'km'),
        'cd' => __d('countries', 'cd'),
        'cg' => __d('countries', 'cg'),
        'kp' => __d('countries', 'kp'),
        'kr' => __d('countries', 'kr'),
        'hr' => __d('countries', 'hr'),
        'cu' => __d('countries', 'cu'),
        'kw' => __d('countries', 'kw'),
        'la' => __d('countries', 'la'),
        'ls' => __d('countries', 'ls'),
        'lv' => __d('countries', 'lv'),
        'lb' => __d('countries', 'lb'),
        'lr' => __d('countries', 'lr'),
        'ly' => __d('countries', 'ly'),
        'li' => __d('countries', 'li'),
        'lt' => __d('countries', 'lt'),
        'lu' => __d('countries', 'lu'),
        'mo' => __d('countries', 'mo'),
        'mg' => __d('countries', 'mg'),
        'mw' => __d('countries', 'mw'),
        'my' => __d('countries', 'my'),
        'mv' => __d('countries', 'mv'),
        'ml' => __d('countries', 'ml'),
        'mt' => __d('countries', 'mt'),
        'ma' => __d('countries', 'ma'),
        'mh' => __d('countries', 'mh'),
        'mq' => __d('countries', 'mq'),
        'mr' => __d('countries', 'mr'),
        'mu' => __d('countries', 'mu'),
        'yt' => __d('countries', 'yt'),
        'mk' => __d('countries', 'mk'),
        'mx' => __d('countries', 'mx'),
        'fm' => __d('countries', 'fm'),
        'md' => __d('countries', 'md'),
        'mc' => __d('countries', 'mc'),
        'mn' => __d('countries', 'mn'),
        'ms' => __d('countries', 'ms'),
        'mz' => __d('countries', 'mz'),
        'mm' => __d('countries', 'mm'),
        'na' => __d('countries', 'na'),
        'nr' => __d('countries', 'nr'),
        'np' => __d('countries', 'np'),
        'nc' => __d('countries', 'nc'),
        'nz' => __d('countries', 'nz'),
        'nt' => __d('countries', 'nt'),
        'ni' => __d('countries', 'ni'),
        'nl' => __d('countries', 'nl'),
        'an' => __d('countries', 'an'),
        'ne' => __d('countries', 'ne'),
        'ng' => __d('countries', 'ng'),
        'nu' => __d('countries', 'nu'),
        'mp' => __d('countries', 'mp'),
        'nf' => __d('countries', 'nf'),
        'no' => __d('countries', 'no'),
        'om' => __d('countries', 'om'),
        'at' => __d('countries', 'at'),
        'pk' => __d('countries', 'pk'),
        'ps' => __d('countries', 'ps'),
        'pw' => __d('countries', 'pw'),
        'pa' => __d('countries', 'pa'),
        'pg' => __d('countries', 'pg'),
        'py' => __d('countries', 'py'),
        'pe' => __d('countries', 'pe'),
        'ph' => __d('countries', 'ph'),
        'pn' => __d('countries', 'pn'),
        'pl' => __d('countries', 'pl'),
        'pt' => __d('countries', 'pt'),
        'pr' => __d('countries', 'pr'),
        're' => __d('countries', 're'),
        'rw' => __d('countries', 'rw'),
        'ro' => __d('countries', 'ro'),
        'ru' => __d('countries', 'ru'),
        'sb' => __d('countries', 'sb'),
        'zm' => __d('countries', 'zm'),
        'ws' => __d('countries', 'ws'),
        'sm' => __d('countries', 'sm'),
        'st' => __d('countries', 'st'),
        'sa' => __d('countries', 'sa'),
        'se' => __d('countries', 'se'),
        'ch' => __d('countries', 'ch'),
        'sn' => __d('countries', 'sn'),
        'cs' => __d('countries', 'cs'),
        'sc' => __d('countries', 'sc'),
        'sl' => __d('countries', 'sl'),
        'zw' => __d('countries', 'zw'),
        'sg' => __d('countries', 'sg'),
        'sk' => __d('countries', 'sk'),
        'si' => __d('countries', 'si'),
        'so' => __d('countries', 'so'),
        'es' => __d('countries', 'es'),
        'lk' => __d('countries', 'lk'),
        'kn' => __d('countries', 'kn'),
        'lc' => __d('countries', 'lc'),
        'pm' => __d('countries', 'pm'),
        'vc' => __d('countries', 'vc'),
        'za' => __d('countries', 'za'),
        'sd' => __d('countries', 'sd'),
        'gs' => __d('countries', 'gs'),
        'sr' => __d('countries', 'sr'),
        'sj' => __d('countries', 'sj'),
        'sz' => __d('countries', 'sz'),
        'sy' => __d('countries', 'sy'),
        'tj' => __d('countries', 'tj'),
        'tw' => __d('countries', 'tw'),
        'tz' => __d('countries', 'tz'),
        'th' => __d('countries', 'th'),
        'tl' => __d('countries', 'tl'),
        'tg' => __d('countries', 'tg'),
        'tk' => __d('countries', 'tk'),
        'to' => __d('countries', 'to'),
        'tt' => __d('countries', 'tt'),
        'ta' => __d('countries', 'ta'),
        'td' => __d('countries', 'td'),
        'cz' => __d('countries', 'cz'),
        'tn' => __d('countries', 'tn'),
        'tr' => __d('countries', 'tr'),
        'tm' => __d('countries', 'tm'),
        'tc' => __d('countries', 'tc'),
        'tv' => __d('countries', 'tv'),
        'ug' => __d('countries', 'ug'),
        'ua' => __d('countries', 'ua'),
        'su' => __d('countries', 'su'),
        'uy' => __d('countries', 'uy'),
        'uz' => __d('countries', 'uz'),
        'vu' => __d('countries', 'vu'),
        'va' => __d('countries', 'va'),
        've' => __d('countries', 've'),
        'ae' => __d('countries', 'ae'),
        'us' => __d('countries', 'us'),
        'gb' => __d('countries', 'gb'),
        'vn' => __d('countries', 'vn'),
        'wf' => __d('countries', 'wf'),
        'cx' => __d('countries', 'cx'),
        'by' => __d('countries', 'by'),
        'eh' => __d('countries', 'eh'),
        'cf' => __d('countries', 'cf'),
        'cy' => __d('countries', 'cy'),
        'hu' => __d('countries', 'hu'),
        'me' => __d('countries', 'me'),
    ],
];

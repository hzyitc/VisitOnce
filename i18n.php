<?php

$langs = [];
foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $l) {
	[ $l, $q ] = array_merge(explode(';q=', $l), [1]);

	$l = strtolower($l);
	if(!preg_match('/^[a-z\-]+$/', $l))
		continue;

	$langs[$l] = floatval($q);
}
arsort($langs);
$langs['en'] = floatval(0);

function i18n(string $name): string {
	global $langs;
	
	foreach($langs as $lang => $_) {
		if(is_file("i18n/${lang}/${name}")) {
			return file_get_contents("i18n/${lang}/${name}");
		}
	}

	return "error";
}

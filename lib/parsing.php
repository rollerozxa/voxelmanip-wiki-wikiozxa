<?php

function parsing($text) {
	$markdown = new ParsedownWiki();
	$markdown->setSafeMode(true);

	$text = $markdown->text($text);

	$text = str_replace('<table>', '<table class="wikitable">', $text);

	$text = preg_replace_callback('@{{ ([\w\d\.]+)\((.+?)\) }}@si', 'parseFunctions', $text);

	return $text;
}

function parseFunctions($match) {
	if (str_contains($match[1], '.') || !file_exists('templates/functions/'.$match[1].'.twig'))
		return '<span class="error">Template error: Invalid function name</span>';

	$data = json_decode($match[2], true);

	if (json_last_error_msg() != "No error")
		return '<span class="error">Template error: '.json_last_error_msg().'</span>';

	return twigloader()->render('functions/'.$match[1].'.twig', [
		'data' => $data
	]);
}

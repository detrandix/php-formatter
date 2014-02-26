<?php

/**
 * This file is part of the PhpFormatter tool
 *
 * Copyright (c) 2014 Tomáš Lang
 */

require __DIR__ . '/Token.php';
require __DIR__ . '/TokenList.php';
require __DIR__ . '/Formatter.php';
require __DIR__ . '/Transformation/ITransformation.php';
require __DIR__ . '/Transformation/Brackets.php';
require __DIR__ . '/Transformation/Constants.php';
require __DIR__ . '/Transformation/CurlyBrackets.php';
require __DIR__ . '/Transformation/Strings/Join.php';
require __DIR__ . '/Transformation/Strings/Semicolon.php';


$options = getopt('p:s:h', []);

if (!array_key_exists('p', $options) || array_key_exists('h', $options)) {
	echo <<<DOC
PHP Formatter (@dev)
--------------------
Usage: {$argv[0]} -p <path to file> [ -s <settings> ]*

Settings:
	brackets@before: 'none', 'whitespace'
	brackets@after: 'none', 'whitespace'
	constants: 'lowercase', 'uppercase'
	curly-brackets@before-first-bracket: 'none', 'whitespace', 'newline', 'newline tab'
	curly-brackets@before-last-bracket: 'none', 'tab'
	curly-brackets/before-content: 'none', 'tab'
	strings/join: 'none', 'whitespace'
	strings/semicolon: 'newline'

DOC;

	exit(0);
}

if (!file_exists($options['p'])) {
	echo 'File "' . $options['p'] . '" not exists.' . PHP_EOL;

	exit(0);
}

$linterOutput = `php -l {$options['p']}`;
if (strpos($linterOutput, 'No syntax errors detected') !== 0) {
	print $linterOutput;

	exit(0);
}

$setting = [];

if (array_key_exists('s', $options)) {
	foreach ((array) $options['s'] as $option) {
		list($key, $value) = explode('=', $option);

		if (strpos($key, '@') !== FALSE) {
			$keys = explode('@', $key);

			$settingRef = &$setting;
			foreach ($keys as $key) {
				if (!isset($settingRef[$key])) {
					$settingRef[$key] = [];
				}
				$settingRef = &$settingRef[$key];
			}
			$settingRef = $value;
		} else {
			$setting[$key] = $value;
		}
	}
}

try {
	$formatter = new PhpFormatter\Formatter($setting);

	echo $formatter->format(file_get_contents($options['p']));
} catch (InvalidArgumentException $e) {
	echo $e->getMessage();
}

echo PHP_EOL;

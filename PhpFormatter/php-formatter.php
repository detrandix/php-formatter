<?php

/**
 * This file is part of the PhpFormatter tool
 *
 * Copyright (c) 2014 Tomáš Lang
 *
 * For the full copyright and license information, please view the file
 * license-mit.txt that was distributed with this source code.
 */

require __DIR__ . '/Token.php';
require __DIR__ . '/TokenList.php';
require __DIR__ . '/Formatter.php';
require __DIR__ . '/ControlStructures.php';
require __DIR__ . '/Indent.php';
require __DIR__ . '/TransformationRules.php';
require __DIR__ . '/Transformation/Braces.php';
require __DIR__ . '/Transformation/NewLine.php';
require __DIR__ . '/Transformation/Spaces.php';


$options = getopt('p:s:h', []);

if (!array_key_exists('p', $options) || array_key_exists('h', $options)) {
	echo <<<DOC
PHP Formatter (@dev)
--------------------
Usage: {$argv[0]} -p <path to file>

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

try {
	$formatter = PhpFormatter\Formatter::createFromSettings();

	echo $formatter->format(file_get_contents($options['p']));
} catch (InvalidArgumentException $e) {
	echo $e->getMessage();
}

echo PHP_EOL;

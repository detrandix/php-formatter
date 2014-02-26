<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$formatter = PhpFormatter\Formatter::createFromSettings([
		'spaces' => ['other' => $settings]
	]);
	return $formatter;
}



$formatter = createFormatter(['before-comma' => TRUE]);

$input = <<<DOC
<?php
\$a = [1,2];
DOC;

$output = <<<DOC
<?php
\$a=[1 ,2];
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['after-comma' => TRUE]);

$input = <<<DOC
<?php
\$a = [1,2];
DOC;

$output = <<<DOC
<?php
\$a=[1, 2];
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['before-semicolon' => TRUE]);

$input = <<<DOC
<?php
test();
DOC;

$output = <<<DOC
<?php
test() ;
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['after-semicolon' => TRUE]);

$input = <<<DOC
<?php
test();test();
DOC;

$output = <<<DOC
<?php
test();
test();
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['after-typecast' => TRUE]);

$input = <<<DOC
<?php
(array)TRUE;(int)TRUE;(string)TRUE;(float)TRUE;(object)TRUE;
DOC;

$output = <<<DOC
<?php
(array) TRUE;
(int) TRUE;
(string) TRUE;
(float) TRUE;
(object) TRUE;
DOC;

Assert::same($output, $formatter->format($input));

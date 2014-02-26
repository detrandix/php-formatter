<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$formatter = PhpFormatter\Formatter::createFromSettings([
		'braces' => $settings,
		'new-line' => [
			'semicolon' => TRUE
		],
		'indent' => [
			'type' => 'tab'
		]
	]);
	return $formatter;
}



$formatter = createFormatter(['while-do' => 'new-line']);

$input = <<<DOC
<?php
while(TRUE){
	test();
}
do{
	test();
}while(TRUE);
DOC;

$output = <<<DOC
<?php
while(TRUE)
{
	test();
}
do
{
	test();
}while(TRUE);
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['while-do' => 'new-line-idented']);

$input = <<<DOC
<?php
while(TRUE){
	test();
}
do{
	test();
} while(TRUE);
DOC;

$output = <<<DOC
<?php
while(TRUE)
	{
	test();
	}
do
	{
	test();
	}while(TRUE);
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['while-do' => 'same-line']);

$input = <<<DOC
<?php
while(TRUE){
	test();
}
do{
	test();
} while(TRUE);
DOC;

$output = <<<DOC
<?php
while(TRUE){
	test();
}
do{
	test();
}while(TRUE);
DOC;

Assert::same($output, $formatter->format($input));

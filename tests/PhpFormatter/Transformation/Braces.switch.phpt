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



$formatter = createFormatter(['switch' => 'new-line']);

$input = <<<DOC
<?php
switch(TRUE){
	break;
}
DOC;

$output = <<<DOC
<?php
switch(TRUE)
{
	break;
}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['switch' => 'new-line-idented']);

$input = <<<DOC
<?php
switch(TRUE){
	break;
}
DOC;

$output = <<<DOC
<?php
switch(TRUE)
	{
	break;
	}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['switch' => 'same-line']);

$input = <<<DOC
<?php
switch(TRUE){
	break;
}
DOC;

$output = <<<DOC
<?php
switch(TRUE){
	break;
}
DOC;

Assert::same($output, $formatter->format($input));

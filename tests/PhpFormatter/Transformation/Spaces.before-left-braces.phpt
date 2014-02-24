<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$formatter = PhpFormatter\Formatter::createFromSettings([
		'spaces' => ['before-left-braces' => $settings]
	]);
	return $formatter;
}



$formatter = createFormatter(['class-declaration' => TRUE]);

$input = <<<DOC
<?php
class Test{}
if(TRUE){}
DOC;

$output = <<<DOC
<?php
class Test {}if(TRUE){}
DOC;

Assert::same($output, $formatter->format($input));


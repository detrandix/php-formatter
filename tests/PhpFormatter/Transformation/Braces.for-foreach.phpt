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



$formatter = createFormatter(['for-foreach' => 'new-line']);

$input = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++){
	print \$i;
}
foreach(\$array as \$el){
	print \$el;
}
DOC;

$output = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++)
{
	print \$i;
}
foreach(\$array as \$el)
{
	print \$el;
}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['for-foreach' => 'new-line-idented']);

$input = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++){
	print \$i;
}
foreach(\$array as \$el){
	print \$el;
}
DOC;

$output = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++)
	{
	print \$i;
	}
foreach(\$array as \$el)
	{
	print \$el;
	}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['for-foreach' => 'same-line']);

$input = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++){
	print \$i;
}
foreach(\$array as \$el){
	print \$el;
}
DOC;

$output = <<<DOC
<?php
for(\$i=0;\$i<10;\$i++){
	print \$i;
}
foreach(\$array as \$el){
	print \$el;
}
DOC;

Assert::same($output, $formatter->format($input));

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



$formatter = createFormatter(['if-elseif-else' => 'new-line']);

$input = <<<DOC
<?php
if(TRUE){
	function test();
}elseif(TRUE){
	function test();
}else{
	function test();
}
DOC;

$output = <<<DOC
<?php
if(TRUE)
{
	function test();
}elseif(TRUE)
{
	function test();
}else
{
	function test();
}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['if-elseif-else' => 'new-line-idented']);

$input = <<<DOC
<?php
if(TRUE){
	function test();
}elseif(TRUE){
	function test();
}else{
	function test();
}
DOC;

$output = <<<DOC
<?php
if(TRUE)
	{
	function test();
	}elseif(TRUE)
	{
	function test();
	}else
	{
	function test();
	}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['if-elseif-else' => 'same-line']);

$input = <<<DOC
<?php
if(TRUE){
	function test();
}elseif(TRUE){
	function test();
}else{
	function test();
}
DOC;

$output = <<<DOC
<?php
if(TRUE){
	function test();
}elseif(TRUE){
	function test();
}else{
	function test();
}
DOC;

Assert::same($output, $formatter->format($input));

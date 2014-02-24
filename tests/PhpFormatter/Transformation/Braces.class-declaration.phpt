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



$formatter = createFormatter(['class-declaration' => 'new-line']);

$input = <<<DOC
<?php
class Test{public function test(){};public function test2(){};}
DOC;

$output = <<<DOC
<?php
class Test
{
	public function test(){};
	public function test2(){};
}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['class-declaration' => 'new-line-idented']);

$input = <<<DOC
<?php
class Test{public function test(){};public function test2(){};}
DOC;

$output = <<<DOC
<?php
class Test
	{
	public function test(){};
	public function test2(){};
	}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['class-declaration' => 'same-line']);

$input = <<<DOC
<?php
class Test{public function test(){};public function test2(){};}
DOC;

$output = <<<DOC
<?php
class Test{
	public function test(){};
	public function test2(){};
}
DOC;

Assert::same($output, $formatter->format($input));

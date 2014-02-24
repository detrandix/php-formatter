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



$formatter = createFormatter(['try-catch' => 'new-line']);

$input = <<<DOC
<?php
try{
	test();
} catch (Exception \$e) {
	test();
}
DOC;

$output = <<<DOC
<?php
try
{
	test();
}catch(Exception\$e)
{
	test();
}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['try-catch' => 'new-line-idented']);

$input = <<<DOC
<?php
try{
	test();
} catch (Exception \$e) {
	test();
}
DOC;

$output = <<<DOC
<?php
try
	{
	test();
	}catch(Exception\$e)
	{
	test();
	}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['try-catch' => 'same-line']);

$input = <<<DOC
<?php
try{
	test();
} catch (Exception \$e) {
	test();
}
DOC;

$output = <<<DOC
<?php
try{
	test();
}catch(Exception\$e){
	test();
}
DOC;

Assert::same($output, $formatter->format($input));

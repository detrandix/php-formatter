<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$spacesTransformation = new PhpFormatter\Transformation\Spaces;
	$formatter  = new PhpFormatter\Formatter;
	$spacesTransformation->registerToFormatter($formatter, [
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
test(); test();
DOC;

Assert::same($output, $formatter->format($input));
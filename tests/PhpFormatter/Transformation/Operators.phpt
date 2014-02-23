<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$operatorsTransformation = new PhpFormatter\Transformation\Operators;
	$formatter  = new PhpFormatter\Formatter;
	$operatorsTransformation->registerToFormatter($formatter, [
		'spaces' => ['arround-operators' => $settings]
	]);
	return $formatter;
}



$formatter = createFormatter(['unary-operators' => TRUE]);

$input = <<<DOC
<?php
\$a++;\$b--;
DOC;

$output = <<<DOC
<?php
\$a ++ ;\$b -- ;
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['binary-operators' => TRUE]);

$input = <<<DOC
<?php
if (TRUE && FALSE || (TRUE >> FALSE));
DOC;

$output = <<<DOC
<?php
if(TRUE && FALSE || (TRUE >> FALSE));
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['ternary-operators' => TRUE]);

$input = <<<DOC
<?php
\$a = (1?2:3) ?:2;
DOC;

$output = <<<DOC
<?php
\$a=(1 ? 2 : 3) ?: 2;
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['string-concation-operator' => TRUE]);

$input = <<<DOC
<?php
\$a = 'a' .'b';
DOC;

$output = <<<DOC
<?php
\$a='a' . 'b';
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['key-value-operator' => TRUE]);

$input = <<<DOC
<?php
\$a = ['a'=>'b'];
DOC;

$output = <<<DOC
<?php
\$a=['a' => 'b'];
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['assignment-operator' => TRUE]);

$input = <<<DOC
<?php
\$a='b';
\$b .= 'c';
DOC;

$output = <<<DOC
<?php
\$a = 'b';\$b .= 'c';
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['object-operator' => TRUE]);

$input = <<<DOC
<?php
\$test->test();
DOC;

$output = <<<DOC
<?php
\$test -> test();
DOC;

Assert::same($output, $formatter->format($input));
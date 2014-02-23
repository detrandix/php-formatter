<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings, $namespace = 'arround-operators')
{
	$operatorsTransformation = new PhpFormatter\Transformation\Operators;
	$formatter  = new PhpFormatter\Formatter;
	$operatorsTransformation->registerToFormatter($formatter, [
		'spaces' => [$namespace => $settings]
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



$formatter = createFormatter(['if-elseif' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
if(TRUE){}elseif(FALSE){}
DOC;

$output = <<<DOC
<?php
if (TRUE){}elseif (FALSE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['for-foreach' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
for(;;){}foreach(range(1,2)){}
DOC;

$output = <<<DOC
<?php
for (;;){}foreach (range(1,2)){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['while' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
while(TRUE){}
DOC;

$output = <<<DOC
<?php
while (TRUE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['catch' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
try{}catch(Exception \$e){}
DOC;

$output = <<<DOC
<?php
try{}catch (Exception\$e){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['switch' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
switch(TRUE){}
DOC;

$output = <<<DOC
<?php
switch (TRUE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['array-declaration' => TRUE], 'before-parentheses');

$input = <<<DOC
<?php
\$a = array('a','b');
DOC;

$output = <<<DOC
<?php
\$a=array ('a','b');
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['before-comma' => TRUE], 'other');

$input = <<<DOC
<?php
\$a = [1,2];
DOC;

$output = <<<DOC
<?php
\$a=[1 ,2];
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['after-comma' => TRUE], 'other');

$input = <<<DOC
<?php
\$a = [1,2];
DOC;

$output = <<<DOC
<?php
\$a=[1, 2];
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['before-semicolon' => TRUE], 'other');

$input = <<<DOC
<?php
test();
DOC;

$output = <<<DOC
<?php
test() ;
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['after-semicolon' => TRUE], 'other');

$input = <<<DOC
<?php
test();test();
DOC;

$output = <<<DOC
<?php
test(); test();
DOC;

Assert::same($output, $formatter->format($input));
<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$formatter = PhpFormatter\Formatter::createFromSettings([
		'spaces' => ['before-parentheses' => $settings]
	]);
	return $formatter;
}



$formatter = createFormatter(['if-elseif' => TRUE]);

$input = <<<DOC
<?php
if(TRUE){}elseif(FALSE){}
DOC;

$output = <<<DOC
<?php
if (TRUE){}elseif (FALSE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['for-foreach' => TRUE]);

$input = <<<DOC
<?php
for(;;){}foreach(range(1,2)){}
DOC;

$output = <<<DOC
<?php
for (;;){}foreach (range(1,2)){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['while' => TRUE]);

$input = <<<DOC
<?php
while(TRUE){}
DOC;

$output = <<<DOC
<?php
while (TRUE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['catch' => TRUE]);

$input = <<<DOC
<?php
try{}catch(Exception \$e){}
DOC;

$output = <<<DOC
<?php
try{}catch (Exception\$e){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['switch' => TRUE]);

$input = <<<DOC
<?php
switch(TRUE){}
DOC;

$output = <<<DOC
<?php
switch (TRUE){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['array-declaration' => TRUE]);

$input = <<<DOC
<?php
\$a = array('a','b');
DOC;

$output = <<<DOC
<?php
\$a=array ('a','b');
DOC;

Assert::same($output, $formatter->format($input));

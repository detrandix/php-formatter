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
DOC;

$output = <<<DOC
<?php
class Test {}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['method-declaration' => TRUE]);

$input = <<<DOC
<?php
function test(){}
DOC;

$output = <<<DOC
<?php
function test() {}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['if-elseif' => TRUE]);

$input = <<<DOC
<?php
if(TRUE){}elseif(FALSE){};
DOC;

$output = <<<DOC
<?php
if(TRUE) {}elseif(FALSE) {};
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['else' => TRUE]);

$input = <<<DOC
<?php
if(TRUE){}else{};
DOC;

$output = <<<DOC
<?php
if(TRUE){}else {};
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['while' => TRUE]);

$input = <<<DOC
<?php
while(TRUE){}
DOC;

$output = <<<DOC
<?php
while(TRUE) {}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['for-foreach' => TRUE]);

$input = <<<DOC
<?php
for(;;){}foreach(\$a as \$b){};
DOC;

$output = <<<DOC
<?php
for(;;) {}foreach(\$a as \$b) {};
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['do' => TRUE]);

$input = <<<DOC
<?php
do{}while(TRUE);
DOC;

$output = <<<DOC
<?php
do {}while(TRUE);
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['switch' => TRUE]);

$input = <<<DOC
<?php
switch(TRUE){}
DOC;

$output = <<<DOC
<?php
switch(TRUE) {}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['try' => TRUE]);

$input = <<<DOC
<?php
try{}catch(Exception \$e){}
DOC;

$output = <<<DOC
<?php
try {}catch(Exception\$e){}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['catch' => TRUE]);

$input = <<<DOC
<?php
try{}catch(Exception \$e){}
DOC;

$output = <<<DOC
<?php
try{}catch(Exception\$e) {}
DOC;

Assert::same($output, $formatter->format($input));

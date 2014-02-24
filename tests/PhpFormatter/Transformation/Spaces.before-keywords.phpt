<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


function createFormatter($settings)
{
	$spacesTransformation = new PhpFormatter\Transformation\Spaces;
	$formatter  = new PhpFormatter\Formatter;
	$spacesTransformation->registerToFormatter($formatter, [
		'spaces' => ['before-keywords' => $settings]
	]);
	return $formatter;
}



$formatter = createFormatter(['while' => TRUE]);

$input = <<<DOC
<?php
while(TRUE){};do{}while(TRUE);
DOC;

$output = <<<DOC
<?php
while(TRUE){};do{} while(TRUE);
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['else-elseif' => TRUE]);

$input = <<<DOC
<?php
if(TRUE){}elseif(TRUE){}else{}
DOC;

$output = <<<DOC
<?php
if(TRUE){} elseif(TRUE){} else{}
DOC;

Assert::same($output, $formatter->format($input));



$formatter = createFormatter(['catch' => TRUE]);

$input = <<<DOC
<?php
try{}catch(Exception \$e){}
DOC;

$output = <<<DOC
<?php
try{} catch(Exception\$e){}
DOC;

Assert::same($output, $formatter->format($input));

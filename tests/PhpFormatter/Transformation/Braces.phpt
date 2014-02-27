<?php

use PhpFormatter\Token;

require_once __DIR__ . '/../../bootstrap.php';


$formatter = PhpFormatter\Formatter::createFromSettings();

$input = <<<DOC
<?php
if(TRUE){if(FALSE){}}
DOC;

$output = <<<DOC
<?php
if(TRUE){
if(FALSE){
}
}
DOC;

Assert::same($output, $formatter->format($input));

$input = <<<DOC
<?php
\$test->{\$a}=1;
DOC;

$output = <<<DOC
<?php
\$test->{\$a}=1;
DOC;

Assert::same($output, $formatter->format($input));

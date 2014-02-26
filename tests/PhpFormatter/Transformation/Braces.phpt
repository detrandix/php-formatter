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

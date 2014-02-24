<?php

require_once __DIR__ . '/../../bootstrap.php';


$formatter = PhpFormatter\Formatter::createFromSettings();

$input = <<<DOC
<?php
class Test{public test;private test;final protected test;}
DOC;

$output = <<<DOC
<?php
class Test{public test;private test;final protected test;}
DOC;

Assert::same($output, $formatter->format($input));



$input = <<<DOC
<?php
function test(){}
DOC;

$output = <<<DOC
<?php
function test(){}
DOC;

Assert::same($output, $formatter->format($input));
<?php

require_once __DIR__ . '/../../bootstrap.php';


$formatter = PhpFormatter\Formatter::createFromSettings();

$input = <<<DOC
<?php
class Test extends AbstractTest implements Countable, AnotherInterface{
	const TEST = 10;public test;static private test;final protected test;
}
DOC;

$output = <<<DOC
<?php
class Test extends AbstractTest implements Countable,AnotherInterface{
const TEST=10;
public test;
static private test;
final protected test;
}
DOC;

Assert::same($output, $formatter->format($input));



$input = <<<DOC
<?php
function test(){}
DOC;

$output = <<<DOC
<?php
function test(){
}
DOC;

Assert::same($output, $formatter->format($input));



$input = <<<DOC
<?php
print"test";echo"test";
DOC;

$output = <<<DOC
<?php
print "test";
echo "test";
DOC;

Assert::same($output, $formatter->format($input));



$input = <<<DOC
<?php
foreach(\$values as \$value){}
DOC;

$output = <<<DOC
<?php
foreach(\$values as \$value){
}
DOC;

Assert::same($output, $formatter->format($input));
<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../bootstrap.php';


$formatter = Formatter::createFromSettings();

$input = <<<DOC
<?php
test();

test2();

DOC;

$output = <<<DOC
<?php
test();

test2();
DOC;

Assert::same($output, $formatter->format($input));


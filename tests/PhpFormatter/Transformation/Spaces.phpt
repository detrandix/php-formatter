<?php

require_once __DIR__ . '/../../bootstrap.php';


$formatter = PhpFormatter\Formatter::createFromSettings();

$input = <<<DOC
<?php
class Test{}
DOC;

$output = <<<DOC
<?php
class Test{}
DOC;

Assert::same($output, $formatter->format($input));

<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../../../bootstrap.php';


$formatter = new Formatter(['strings/semicolon' => 'newline']);

$original = <<<DOC
<?php

test();
someFunction();\$b = 'a';
DOC;

$expected = <<<DOC
<?php

test();
someFunction();
\$b = 'a';

DOC;

Assert::same($expected, $formatter->format($original));

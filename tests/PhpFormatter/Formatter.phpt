<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../bootstrap.php';


$formatter = new Formatter();

$original = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expected = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['constants' => 'uppercase']);

$original = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expected = <<<DOC
<?php

if (\$a > NULL) {
    echo "a"."b";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['strings/join' => 'whitespace']);

$original = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expected = <<<DOC
<?php

if (\$a > null) {
    echo "a" . "b";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['before' => 'none']]);

$original = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expected = <<<DOC
<?php

if(\$a > null) {
    echo "a"."b";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['inside' => 'whitespace']]);

$original = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expected = <<<DOC
<?php

if ( \$a > null ) {
    echo "a"."b";
}
DOC;

Assert::same($expected, $formatter->format($original));

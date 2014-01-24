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

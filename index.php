<?php

require_once dirname('.') . '/Formatter.php';
require_once dirname('.') . '/Token.php';
require_once dirname('.') . '/TokenQueue.php';

$formatter = new Formatter;

$originalCode = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$expectedCode = <<<DOC
<?php

if( \$a > NULL ) {
    echo "a" . "b";
}
DOC;

$translatedCode = $formatter->format($originalCode);

var_dump($translatedCode === $expectedCode);

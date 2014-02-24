<?php

include __DIR__ . '/vendor/autoload.php';

$formatter  = new PhpFormatter\Formatter;


$test = <<<DOC
<?php
\$a = (int) 1;
DOC;
print $formatter->format($test);
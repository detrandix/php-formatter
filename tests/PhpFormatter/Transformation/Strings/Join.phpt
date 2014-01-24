<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../../../bootstrap.php';


$formatter = new Formatter(['strings/join' => 'whitespace']);

$original = <<<DOC
<?php

echo \$a."b";
DOC;

$expected = <<<DOC
<?php

echo \$a . "b";
DOC;

Assert::same($expected, $formatter->format($original));




$formatter = new Formatter(['strings/join' => 'none']);

$original = <<<DOC
<?php

echo "a" . \$b;
DOC;

$expected = <<<DOC
<?php

echo "a".\$b;
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['strings/join' => 'left']);

$original = <<<DOC
<?php

echo "a" . "b";
DOC;

$expected = <<<DOC
<?php

echo "a" ."b";
DOC;

Assert::same($expected, $formatter->format($original));




$formatter = new Formatter(['strings/join' => 'right']);

$original = <<<DOC
<?php

echo \$a . \$b;
DOC;

$expected = <<<DOC
<?php

echo \$a. \$b;
DOC;

Assert::same($expected, $formatter->format($original));

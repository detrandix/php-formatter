<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../../bootstrap.php';


$formatter = new Formatter(['brackets' => ['before' => 'none']]);

$original = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if(true) {
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['before' => 'whitespace']]);

$original = <<<DOC
<?php

if(true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['inside' => 'whitespace']]);

$original = <<<DOC
<?php

if(true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if( true ) {
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['inside' => 'whitespace', 'before' => 'none']]);

$original = <<<DOC
<?php

if ((\$a & \$b) || ((true) || (false))) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if( ( \$a & \$b ) || ( ( true ) || ( false ) ) ) {
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['brackets' => ['inside' => 'none']]);

$original = <<<DOC
<?php

if ( true ) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));

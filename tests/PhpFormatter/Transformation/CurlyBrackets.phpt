<?php

use PhpFormatter\Formatter;

require_once __DIR__ . '/../../bootstrap.php';


$formatter = new Formatter(['curly-brackets' => ['before-first-bracket' => 'none']]);

$original = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if (true){
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['curly-brackets' => ['before-first-bracket' => 'whitespace']]);

$original = <<<DOC
<?php

if (true){
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



$formatter = new Formatter(['curly-brackets' => ['before-first-bracket' => 'newline']]);

$original = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if (true)
{
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['curly-brackets' => ['before-first-bracket' => 'newline tab']]);

$original = <<<DOC
<?php

if (true) {
	echo "true";
}
DOC;

$expected = <<<DOC
<?php

if (true)
	{
	echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['curly-brackets' => ['before-last-bracket' => 'none']]);

$original = <<<DOC
<?php

if (true) {
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



$formatter = new Formatter(['curly-brackets' => ['before-first-bracket' => 'newline', 'before-last-bracket' => 'none']]);

$original = <<<DOC
<?php

if (true) {echo "true";}
DOC;

$expected = <<<DOC
<?php

if (true)
{
echo "true";
}
DOC;

Assert::same($expected, $formatter->format($original));



$formatter = new Formatter(['curly-brackets' => ['before-last-bracket' => 'tab']]);

$original = <<<DOC
<?php

if (true) {
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

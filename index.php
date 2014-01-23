<?php

require_once dirname('.') . '/Formatter.php';
require_once dirname('.') . '/Token.php';
require_once dirname('.') . '/TokenQueue.php';

$testing = array(
	array(
		'settings' => [],
		'original' => '
<?php

if ($a > null) {
    echo "a"."b";
}',
		'expected' => '
<?php

if ($a > null) {
    echo "a"."b";
}'
	),
	array(
		'settings' => ['constants' => ['uppercase' => TRUE]],
		'original' => '
<?php

if ($a > null) {
    echo "a"."b";
}',
		'expected' => '
<?php

if ($a > NULL) {
    echo "a"."b";
}'
	),
	array(
		'settings' => ['strings' => ['join' => 'whitespace']],
		'original' => '
<?php

if ($a > null) {
    echo "a"."b";
}',
		'expected' => '
<?php

if ($a > null) {
    echo "a" . "b";
}'
	),
	array(
		'settings' => ['if' => ['before bracket' => 'none']],
		'original' => '
<?php

if ($a > null) {
    echo "a"."b";
}',
		'expected' => '
<?php

if($a > null) {
    echo "a"."b";
}'
	)
);

foreach ($testing as $test) {
	$formatter = new Formatter($test['settings']);

	var_dump($test['expected'] === $formatter->format($test['original']));
}

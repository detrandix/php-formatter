<?php

class Formatter
{

	public function format($code)
	{
		$tokens = token_get_all($code);

		$this->render($this->processTokens($tokens, 0, count($tokens) - 1));
	}

	protected function processTokens($originalTokens, $startIndex, $endIndex)
	{
		$tokens = [];

		for ($i=$startIndex; $i <= $endIndex; $i++) {
			$token = $originalTokens[$i];

			if (is_array($token)) {
				// zvetseni konstant
				if (token_name($token[0]) === 'T_STRING' && in_array(strtolower($token[1]), array('null', 'true', 'false'))) {
					$token[1] = strtoupper($token[1]);
					$tokens[] = $token;
				} elseif (token_name($token[0]) === 'T_IF') {
					$tokens[] = $token;

					$nextToken = $originalTokens[$i + 1];

					// odstraneni mezery v IF pred zavorkou
					if ($this->isTokenType($nextToken, 'T_WHITESPACE')) {
						$i++;
					}

					$tokens[] = '(';

					$tokens[] = ' '; // mezera po zavorce

					$position = $this->findPositionOfClosingBracket($originalTokens, $i + 1);

					$tokens = array_merge($tokens, $this->processTokens($originalTokens, $i + 2, $position - 1));

					$tokens[] = ' '; // mezera pred zavorce

					$tokens[] = ')';

					$i = $position;
				} elseif ($this->isTokenType($token, 'T_CONSTANT_ENCAPSED_STRING')) {
					$tokens[] = $token;

					$nextToken = $originalTokens[$i + 1];

					if ($nextToken === '.') {
						$tokens[] = ' . ';
						$i++;
					}
				} else {
					$tokens[] = $token;
				}
			} else {
				$tokens[] = $token;
			}
		}

		return $tokens;
	}

	protected function findPositionOfClosingBracket($tokens, $from)
	{
		$level = 0;
		for ($i=$from, $count=count($tokens); $i < $count; $i++) {
			if ($tokens[$i] === '(') {
				$level++;
			} elseif ($tokens[$i] === ')') {
				$level--;
			}

			if ($level === 0) {
				return $i;
			}
		}
	}

	protected function isTokenType($token, $type)
	{
		return is_array($token) && token_name($token[0]) === $type;
	}

	protected function render($tokens)
	{
		foreach ($tokens as $token)
		{
			if (is_array($token)) {
				print $token[1];
			} else {
				print $token;
			}
		}
	}

}

$code = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$formatter = new Formatter;

$formatter->format($code);

$expectedCode = <<<DOC
<?php

if( \$a > NULL ) {
    echo "a" . "b";
}
DOC;

var_dump($code === $expectedCode);

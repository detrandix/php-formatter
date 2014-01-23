<?php

class Token
{

	protected $value;

	protected $type;

	public function __construct($value, $type = NULL)
	{
		$this->value = $value;
		$this->type = $type;
	}

	public static function createFromZendToken($zendToken)
	{
		if (is_array($zendToken)) {
			$value = $zendToken[1];
			$type = $zendToken[0];
		} else {
			$value = $zendToken;
			$type = NULL;
		}

		return new self($value, $type);
	}

	public function isType($type)
	{
		if (is_int($type)) {
			return $type === $this->type;
		} else {
			return (string) $type === token_name($this->type);
		}
	}

	public function isSingleValue($value)
	{
		return $this->type === NULL && $this->value === $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function __toString()
	{
		return $this->value;
	}

}

class TokenQueue extends SplQueue
{

	public function __construct(array $queue = [])
	{
		foreach ($queue as $token) {
			$this[] = $token;
		}
	}

	public function dequeue()
	{
		$token = parent::dequeue();

		return $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

	public function bottom()
	{
		$token = parent::bottom();

		return $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

}

class Formatter
{

	public function format($code)
	{
		$tokenQueue = new TokenQueue(token_get_all($code));

		return $this->render($this->processTokenQueue($tokenQueue));
	}

	protected function processTokenQueue(TokenQueue $tokenQueue)
	{
		$processedTokenQueue = new TokenQueue;

		while (!$tokenQueue->isEmpty()) {
			$token = $tokenQueue->dequeue();

			if ($token->isType('T_STRING') && in_array(strtolower($token->getValue()), ['null', 'true', 'false'])) { // zvetseni konstant
				$token->setValue(strtoupper($token->getValue()));

				$processedTokenQueue[] = $token;
			} elseif ($token->isType('T_CONSTANT_ENCAPSED_STRING')) {
				$processedTokenQueue[] = $token;

				if ($tokenQueue->bottom()->isSingleValue('.'))
				{
					$processedTokenQueue[] = new Token(' ', 'T_WHITESPACE');
					$processedTokenQueue[] = $tokenQueue->dequeue(); // mezery kolem spojovani stringu
					$processedTokenQueue[] = new Token(' ', 'T_WHITESPACE');
				}
			} elseif ($token->isType('T_IF')) {
				$processedTokenQueue[] = $token;

				if ($tokenQueue->bottom()->isType('T_WHITESPACE')) { // odstraneni mezery v IF pred zavorkou
					$tokenQueue->dequeue();
				}

				$bracketInnerQueue = new TokenQueue;
				$level = 0;
				do {
					$innerToken = $tokenQueue->dequeue();

					if ($innerToken->isSingleValue('(')) {
						if ($level > 0) {
							$bracketInnerQueue[] = $innerToken;
						}

						$level++;
					} elseif ($innerToken->isSingleValue(')')) {
						if ($level > 1) {
							$bracketInnerQueue[] = $innerToken;
						}

						$level--;
					} else {
						$bracketInnerQueue[] = $innerToken;
					}
				} while ($level > 0);

				$processedTokenQueue[] = '(';
				$processedTokenQueue[] = ' '; // nepovinne

				foreach ($this->processTokenQueue($bracketInnerQueue) as $processedToken) {
					$processedTokenQueue[] = $processedToken;
				}
				$processedTokenQueue[] = ' '; // nepovinne
				$processedTokenQueue[] = ')';
			} else {
				$processedTokenQueue[] = $token;
			}
		}

		return $processedTokenQueue;
	}

	protected function render(TokenQueue $tokenQueue)
	{
		$string = '';
		foreach ($tokenQueue as $token)
		{
			$string .= $token;
		}
		return $string;
	}

}

$code = <<<DOC
<?php

if (\$a > null) {
    echo "a"."b";
}
DOC;

$formatter = new Formatter;

$translatedCode = $formatter->format($code);

$expectedCode = <<<DOC
<?php

if( \$a > NULL ) {
    echo "a" . "b";
}
DOC;

var_dump($translatedCode === $expectedCode);

<?php

class Formatter
{

	protected $settings;

	public function __construct($settings)
	{
		$this->settings = $settings;
	}

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

			if (isset($this->settings['constants']['uppercase']) && $token->isType('T_STRING') && in_array(strtolower($token->getValue()), ['null', 'true', 'false'])) { // zvetseni konstant
				$token->setValue(strtoupper($token->getValue()));

				$processedTokenQueue[] = $token;
			} elseif ($token->isType('T_CONSTANT_ENCAPSED_STRING')) {
				$processedTokenQueue[] = $token;

				if (isset($this->settings['strings']['join']) && $this->settings['strings']['join'] === 'whitespace' && $tokenQueue->bottom()->isSingleValue('.'))
				{
					$processedTokenQueue[] = new Token(' ', 'T_WHITESPACE');
					$processedTokenQueue[] = $tokenQueue->dequeue(); // mezery kolem spojovani stringu
					$processedTokenQueue[] = new Token(' ', 'T_WHITESPACE');
				}
			} elseif ($token->isType('T_IF')) {
				$processedTokenQueue[] = $token;

				if ($tokenQueue->bottom()->isType('T_WHITESPACE')) { // odstraneni mezery v IF pred zavorkou
					$token = $tokenQueue->dequeue();
					if (!(isset($this->settings['if']['before bracket']) && $this->settings['if']['before bracket'] === 'none')) {
						$processedTokenQueue[] = $token;
					}
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
				//$processedTokenQueue[] = ' '; // nepovinne

				foreach ($this->processTokenQueue($bracketInnerQueue) as $processedToken) {
					$processedTokenQueue[] = $processedToken;
				}
				//$processedTokenQueue[] = ' '; // nepovinne
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

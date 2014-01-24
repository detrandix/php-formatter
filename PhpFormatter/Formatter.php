<?php

namespace PhpFormatter;

class Formatter
{

	protected $settings;

	protected $transformations;

	public function __construct($settings = [])
	{
		$this->settings = $settings;

		$this->transformations = [];

		foreach ($settings as $key => $value) {
			if ($key === 'constants') {
				$this->transformations[] = new Transformation\Constants($value);
			} elseif ($key === 'strings/join') {
				$this->transformations[] = new Transformation\Strings\Join($value);
			}
		}
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

			$transformed = FALSE;
			foreach ($this->transformations as $transformation) {
				if ($transformation->canApply($token, $tokenQueue)) {
					$transformation->transform($token, $tokenQueue, $processedTokenQueue);
					$transformed = TRUE;
					break;
				}
			}

			if ($transformed) {

			} elseif ($token->isType(T_IF)) {
				$processedTokenQueue[] = $token;

				if ($tokenQueue->bottom()->isType(T_WHITESPACE)) { // odstraneni mezery v IF pred zavorkou
					$token = $tokenQueue->dequeue();

					if (!$this->allowed('if/before-brackets', 'none')) {
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
				if ($this->allowed('if/inside-brackets', 'whitespace')) {
					$processedTokenQueue[] = ' ';
				}

				foreach ($this->processTokenQueue($bracketInnerQueue) as $processedToken) {
					$processedTokenQueue[] = $processedToken;
				}

				if ($this->allowed('if/inside-brackets', 'whitespace')) {
					$processedTokenQueue[] = ' ';
				}
				$processedTokenQueue[] = ')';
			} else {
				$processedTokenQueue[] = $token;
			}
		}

		return $processedTokenQueue;
	}

	protected function allowed($action, $value)
	{
		$actions = explode('/', $action);

		$settings = $this->settings;

		$error = FALSE;
		while (count($actions) > 0) {
			$action = array_shift($actions);
			if (isset($settings[$action])) {
				$settings = $settings[$action];
			} else {
				$error = TRUE;
				break;
			}
		}

		if (!$error) {
			return $settings === $value;
		} else {
			return FALSE;
		}
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

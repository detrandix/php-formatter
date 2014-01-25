<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenQueue;
use PhpFormatter\Formatter;

class CurlyBrackets implements ITransformation
{

	protected $setting;

	public function __construct($setting)
	{
		$this->setting = ['before-first-bracket' => NULL, 'before-last-bracket' => NULL, 'before-content' => NULL];

		foreach ((array) $setting as $key => $value) {
			if ($key === 'before-first-bracket') {
				if (!($value === NULL || in_array($value, ['none', 'whitespace', 'newline', 'newline tab']))) {
					throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}'.");
				}

				$this->setting[$key] = $value;
			} elseif ($key === 'before-last-bracket') {
				if (!($value === NULL || in_array($value, ['none', 'tab']))) {
					throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}'.");
				}

				$this->setting[$key] = $value;
			} elseif ($key === 'before-content') {
				if (!($value === NULL || in_array($value, ['none', 'tab']))) {
					throw new \InvalidArgumentException("Unknown setting '{$value}' for key '{$key}'.");
				}

				$this->setting[$key] = $value;
			}
		}
	}

	public function canApply(Token $token, TokenQueue $queue)
	{
		return $token->isSingleValue('{') || ($token->isType(T_WHITESPACE) && $queue->count() > 0 && $queue->bottom()->isSingleValue('{'));
	}

	public function transform(Token $token, TokenQueue $inputQueue, TokenQueue $outputQueue, Formatter $formatter)
	{
		switch ($this->setting['before-first-bracket']) {
			case 'none':
				if ($token->isType(T_WHITESPACE)) {
					$token = $inputQueue->dequeue();
				}
				break;
			case 'whitespace':
				if ($token->isSingleValue('{')) {
					$outputQueue[] = new Token(' ', T_WHITESPACE);
				} else {
					$token->setValue(' ');
					$outputQueue[] = $token;
					$token = $inputQueue->dequeue();
				}
				break;
			case 'newline':
				if ($token->isSingleValue('{')) {
					$outputQueue[] = new Token("\n", T_WHITESPACE);
				} else {
					$token->setValue("\n");
					$outputQueue[] = $token;
					$token = $inputQueue->dequeue();
				}
				break;
			case 'newline tab':
				if ($token->isSingleValue('{')) {
					$outputQueue[] = new Token("\n\t", T_WHITESPACE);
				} else {
					$token->setValue("\n\t");
					$outputQueue[] = $token;
					$token = $inputQueue->dequeue();
				}
				break;
			default:
				if ($token->isType(T_WHITESPACE)) {
					$outputQueue[] = $token;
					$token = $inputQueue->dequeue();
				}
				break;
		}

		$outputQueue[] = $token;

		if (!$inputQueue->bottom()->isType(T_WHITESPACE)) {
			$outputQueue[] = new Token("\n", T_WHITESPACE);
		}

		$bracketInnerQueue = new TokenQueue;
		$level = 1;
		do {
			$innerToken = $inputQueue->dequeue();

			if ($innerToken->isSingleValue('{')) {
				if ($level > 0) {
					$bracketInnerQueue[] = $innerToken;
				}

				$level++;
			} elseif ($innerToken->isSingleValue('}')) {
				if ($level > 1) {
					$bracketInnerQueue[] = $innerToken;
				}

				$level--;
			} else {
				$bracketInnerQueue[] = $innerToken;
			}
		} while ($level > 0);

		// @todo spravneho odsazeni vnitrniho obsahu
		if ($this->setting['before-content'] !== NULL) {
			$lines = $this->splitTokensToLines($bracketInnerQueue);
		}

		foreach ($formatter->processTokenQueue($bracketInnerQueue) as $processedToken) {
			$outputQueue[] = $processedToken;
		}

		switch ($this->setting['before-last-bracket']) {
			case 'none':
				if ($outputQueue->top()->isType(T_WHITESPACE)) {
					$token = $outputQueue->pop();

					if (strpos($token->getValue(), "\n") !== FALSE) { // @todo toto jeste doresit
						$outputQueue[] = new Token("\n", T_WHITESPACE);
					}
				} else {
					$outputQueue[] = new Token("\n", T_WHITESPACE);
				}
				break;
			case 'tab':
				if ($outputQueue->top()->isType(T_WHITESPACE)) {
					if ($outputQueue->top()->getValue() !== "\n\t") {
						$token = $outputQueue->pop();
						$token->setValue("\n\t");
						$outputQueue[] = $token;
					}
				} else {
					$outputQueue[] = new Token("\n\t", T_WHITESPACE);
				}
				break;
		}

		$outputQueue[] = $innerToken;
	}

	protected function splitTokensToLines(TokenQueue $inputQueue)
	{
		$lines = array(0 => array());
		foreach ($inputQueue as $token) {
			if ($token->isType(T_WHITESPACE)) {
				if (strpos($token->getValue(), "\n")) {
					$strings = explode("\n", $token->getValue());
					$count = count($strings);
					foreach ($strings as $i => $string) {
						if ($i + 1 < $count) {
							$lines[count($lines) - 1][] = new Token($string . "\n", T_WHITESPACE);
						} else {
							$lines[count($lines) - 1][] = new Token($string, T_WHITESPACE);
						}
					}
				}
			} else {
				$lines[count($lines) - 1][] = $token;
			}
		}
	}

}

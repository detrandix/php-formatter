<?php

namespace PhpFormatter\Transformation;

use PhpFormatter\Token;
use PhpFormatter\TokenList;
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

	public function canApply(Token $token, TokenList $tokenList)
	{
		return $token->isSingleValue('{') || ($token->isType(T_WHITESPACE) && $tokenList->count() > 0 && $tokenList->head()->isSingleValue('{'));
	}

	public function transform(Token $token, TokenList $inputTokenList, TokenList $outputTokenList, Formatter $formatter)
	{
		switch ($this->setting['before-first-bracket']) {
			case 'none':
				if ($token->isType(T_WHITESPACE)) {
					$token = $inputTokenList->shift();
				}
				break;
			case 'whitespace':
				if ($token->isSingleValue('{')) {
					$outputTokenList[] = new Token(' ', T_WHITESPACE);
				} else {
					$token->setValue(' ');
					$outputTokenList[] = $token;
					$token = $inputTokenList->shift();
				}
				break;
			case 'newline':
				if ($token->isSingleValue('{')) {
					$outputTokenList[] = new Token("\n", T_WHITESPACE);
				} else {
					$token->setValue("\n");
					$outputTokenList[] = $token;
					$token = $inputTokenList->shift();
				}
				break;
			case 'newline tab':
				if ($token->isSingleValue('{')) {
					$outputTokenList[] = new Token("\n\t", T_WHITESPACE);
				} else {
					$token->setValue("\n\t");
					$outputTokenList[] = $token;
					$token = $inputTokenList->shift();
				}
				break;
			default:
				if ($token->isType(T_WHITESPACE)) {
					$outputTokenList[] = $token;
					$token = $inputTokenList->shift();
				}
				break;
		}

		$outputTokenList[] = $token;

		if (!$inputTokenList->head()->isType(T_WHITESPACE)) {
			$outputTokenList[] = new Token("\n", T_WHITESPACE);
		}

		$bracketInnerTokenList = new TokenList;
		$level = 1;
		do {
			$innerToken = $inputTokenList->shift();

			if ($innerToken->isSingleValue('{')) {
				if ($level > 0) {
					$bracketInnerTokenList[] = $innerToken;
				}

				$level++;
			} elseif ($innerToken->isSingleValue('}')) {
				if ($level > 1) {
					$bracketInnerTokenList[] = $innerToken;
				}

				$level--;
			} else {
				$bracketInnerTokenList[] = $innerToken;
			}
		} while ($level > 0);

		// @todo spravneho odsazeni vnitrniho obsahu
		if ($this->setting['before-content'] !== NULL) {
			$lines = $this->splitTokensToLines($bracketInnerTokenList);
		}

		foreach ($formatter->processTokenList($bracketInnerTokenList) as $processedToken) {
			$outputTokenList[] = $processedToken;
		}

		switch ($this->setting['before-last-bracket']) {
			case 'none':
				if ($outputTokenList->tail()->isType(T_WHITESPACE)) {
					$token = $outputTokenList->pop();

					if (strpos($token->getValue(), "\n") !== FALSE) { // @todo toto jeste doresit
						$outputTokenList[] = new Token("\n", T_WHITESPACE);
					}
				} else {
					$outputTokenList[] = new Token("\n", T_WHITESPACE);
				}
				break;
			case 'tab':
				if ($outputTokenList->tail()->isType(T_WHITESPACE)) {
					if ($outputTokenList->tail()->getValue() !== "\n\t") {
						$token = $outputTokenList->pop();
						$token->setValue("\n\t");
						$outputTokenList[] = $token;
					}
				} else {
					$outputTokenList[] = new Token("\n\t", T_WHITESPACE);
				}
				break;
		}

		$outputTokenList[] = $innerToken;
	}

	protected function splitTokensToLines(TokenList $inputTokenList)
	{
		$lines = array(0 => array());
		foreach ($inputTokenList as $token) {
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

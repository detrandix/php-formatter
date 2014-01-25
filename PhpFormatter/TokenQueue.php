<?php

namespace PhpFormatter;

class TokenQueue extends \SplQueue
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

	public function pop()
	{
		$token = parent::pop();

		return $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

	public function bottom()
	{
		$token = parent::bottom();

		return $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

	public function top()
	{
		$token = parent::top();

		return $token instanceof Token ? $token : Token::createFromZendToken($token);
	}

}

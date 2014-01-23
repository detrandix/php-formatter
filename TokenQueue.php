<?php

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

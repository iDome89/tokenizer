<?php

declare(strict_types=1);

namespace Mathematicator\Tokenizer\Token;


use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Tokenizer\Tokens;
use Mathematicator\Tokenizer\TokensToObject;
use Nette\SmartObject;
use Nette\Tokenizer\Token;

class SubToken extends BaseToken
{

	use SmartObject;

	/**
	 * @var IToken[]
	 */
	private $tokens = [];

	/**
	 * @var TokensToObject
	 */
	private $tokensToObject;

	/**
	 * @param TokensToObject $tokensToObject
	 */
	public function __construct(TokensToObject $tokensToObject)
	{
		$this->tokensToObject = $tokensToObject;
	}

	/**
	 * @param IToken[] $tokens
	 * @throws MathematicatorException
	 */
	public function setObjectTokens(array $tokens = null): void
	{
		foreach ($tokens as $token) {
			if (!$token instanceof IToken && $token !== null) {
				throw new MathematicatorException(
					'All tokens must be instance of "' . IToken::class . '". '
					. json_encode($token) . ' given.'
				);
			}
		}

		$this->tokens = $tokens;
	}

	/**
	 * Set token array and convert to object array.
	 *
	 * @param Token[] $tokens
	 * @param int $currentPosition
	 * @return int
	 */
	public function setArrayTokens(array $tokens, int $currentPosition): int
	{
		$buffer = [];
		$level = 0;
		$first = true;

		for ($iterator = $currentPosition; isset($tokens[$iterator]); $iterator++) {
			$token = $tokens[$iterator];

			if ($token->type === Tokens::M_LEFT_BRACKET || $token->type === Tokens::M_FUNCTION) {
				$level++;
			} elseif ($token->type === Tokens::M_RIGHT_BRACKET) {
				$level--;
			}

			if ($level > 0) {
				if (!$first) {
					$buffer[] = $token;
				}
			} else {
				break;
			}

			$first = false;
		}

		$this->tokens = $this->tokensToObject->toObject($buffer);

		return $iterator;
	}

	/**
	 * @return IToken[]
	 */
	public function getTokens(): array
	{
		return $this->tokens;
	}

}

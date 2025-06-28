<?php

namespace CommandParser\Specs;

use CommandParser\Exceptions\OptionTokenAlreadyDefinedException;

/**
 * Option Specification
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Option {

    /**
     * The tokens of this option.
     *
     * @internal
     * @since 1.0.0
     * @var array<OptionToken> $tokens
     */
    private array $tokens;

    /**
     * Creates a new option specification.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string  $name
     * @param ?string $description
     * @param bool    $isRepeatable
     * @param bool    $isFlag
     * @param array   $tokens
     */
    public final function __construct(

        public readonly string  $name,
        public readonly ?string $description  = null,
        public readonly bool    $isRepeatable = false,
        public readonly bool    $isFlag       = false,
        array $tokens
    ) {

        $this->tokens = [];

        array_walk($tokens, $this->addToken(...));
    }

    /**
     * Adds a token to this option.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param OptionToken $token
     * @return static
     * 
     * @throws OptionTokenAlreadyDefinedException
     */
    public final function addToken(OptionToken $token): static {

        if (!is_null($this->getToken($token->token, $token->type))) {

            throw new OptionTokenAlreadyDefinedException(token: $token->type->value . $token->token, option: $token->token);
        }

        $this->tokens[] = $token;

        return $this;
    }

    /**
     * Checks if this option has a token with the given name and type.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string            $token
     * @param ?OptionTokenType $type
     * @return bool
     */
    public final function hasToken(string $token, ?OptionTokenType $type = null): bool {

        return !is_null($this->getToken($token, $type));
    }

    /**
     * Gets the token with the given name and type.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string            $token
     * @param ?OptionTokenType  $type
     * @return ?OptionToken
     */
    public final function getToken(string $token, ?OptionTokenType $type = null): ?OptionToken {

        foreach ($this->tokens as $optionToken) {

            if ($optionToken->token == $token && (is_null($type) || $optionToken->type == $type)) {

                return $optionToken;
            }
        }

        return null;
    }
}

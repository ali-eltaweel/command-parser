<?php

namespace CommandParser\Specs;

/**
 * Commandline Option Token Specification.
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class OptionToken {

    /**
     * Creates a new option token specification.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string          $token
     * @param OptionTokenType $type
     */
    public final function __construct(public readonly string  $token, public readonly OptionTokenType $type) {}
}

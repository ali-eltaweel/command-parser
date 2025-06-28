<?php

namespace CommandParser;

/**
 * Parsed Commandline Option.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.1.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Option {

    /**
     * Creates a new Option.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.1.0
     *
     * @param string $name   The name of the option.
     * @param ?array $values The values of the option, if any.
     */
    public final function __construct(public readonly string $name, public readonly ?array $values = null) {}
}

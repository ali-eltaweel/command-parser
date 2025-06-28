<?php

namespace CommandParser\Exceptions;

/**
 * Missing Option Argument Exception.
 * 
 * This exception is thrown by the parser if an option that requires an argument is provided without one.
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class MissingOptionArgumentException extends CommandLineException {

    /**
     * Creates a new Missing Option Argument Exception.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string $command      The name of the command that defines the option.
     * @param string $optionToken  The token of the option that is missing an argument.
     */
    public final function __construct(public readonly string $command, public readonly string $optionToken) {

        parent::__construct(
            "Missing argument for option '{$optionToken}' of command '{$command}'."
        );
    }
}

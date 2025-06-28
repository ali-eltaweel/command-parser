<?php

namespace CommandParser\Exceptions;

/**
 * Option Repetition Denied Exception.
 * 
 * This exception is thrown by the parser if an option that is not repeatable is provided more than once on a commandline.
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class OptionRepetitionDeniedException extends CommandLineException {

    /**
     * Creates a new Option Repetition Denied Exception.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string $command      The name of the command that defines the option.
     * @param string $optionName   The name of the option that cannot be repeated.
     */
    public final function __construct(public readonly string $command, public readonly string $optionName) {

        parent::__construct(
            "Option '{$optionName}' cannot be repeated in command '{$command}'.",
        );
    }
}

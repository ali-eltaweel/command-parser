<?php

namespace CommandParser\Exceptions;

/**
 * Option Not Found Exception.
 * 
 * This exception is thrown by the parser if an option is not found in the command specifications.
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class OptionNotFoundException extends CommandLineException {

    /**
     * Creates a new Option Not Found Exception.
     *
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string $option  The name of the option that was not found.
     * @param string $command The name of the command that defines the option.
     */
    public final function __construct(public readonly string $option, public readonly string $command) {

        parent::__construct("Unknown option '$option' for command '$command'");
    }
}

<?php

namespace CommandParser\Exceptions;

/**
 * Commandline Option Already Defined Exception.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class CommandOptiondAlreadyDefinedException extends CommandLineException {

    /**
     * Creates a new Command Option Already Defined Exception.
     *
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string $option      The name of the option that is already defined.
     * @param string $commandName The name of the command that defines the option.
     */
    public final function __construct(public readonly string $option, public readonly string $commandName) {

        parent::__construct("The option '$option' is already defined in the command '$commandName'");
    }
}

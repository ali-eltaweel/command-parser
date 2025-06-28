<?php

namespace CommandParser\Exceptions;

/**
 * Commandline Option's Token Already Defined Exception.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class OptionTokenAlreadyDefinedException extends CommandLineException {

    /**
     * Creates a new Option Token Already Defined Exception.
     *
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string      $token       The token that is already defined.
     * @param string      $option      The name of the option that the token is defined for.
     * @param string|null $commandName The name of the command that defines the option, if any.
     */
    public final function __construct(public readonly string $token, public readonly string $option, public readonly ?string $commandName = null) {

        if (is_null($commandName)) {

            parent::__construct("The option token '$token' is already defined for option '$option'");
        } else {

            parent::__construct("The option token '$token' is already defined for option '$option' in command '$commandName'");
        }
    }
}
